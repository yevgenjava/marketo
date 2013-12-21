#!/usr/bin/perl
# Copyright: Yevgen Golubenko yevgen.java@gmail.com
# Re-creates database for testing purposes
use strict;
use warnings;
use Term::ANSIColor;
use File::Find;

my $CRED_FILE = "db_credentials.txt";
my $MYSQL_CMD = "mysql";
my $DEFAULT_OPTIONS = 0;
my $SEED_DATA_ONLY = 0;

print "[INFO] Recreating all databases on all hosts\n\n";
&main;
print "\n\n-----------------------------------------------------------------\n" . 
	"[INFO] If you got no errors from MySQL/this script, then its successfull\n";

sub main
{
	my @args = @ARGV;
	foreach my $a(@args)
	{ 
		print "Command line argument: $a\n";
		if($a eq "default")
		{
			$DEFAULT_OPTIONS = "yes";
			print color("red") . "Running w/o prompt, accepting default answers" . color("reset") . "\n";
		}

		if($a eq "seed" || $a eq "sd" || $a eq "data" || $a eq "seeddata")
		{
			$SEED_DATA_ONLY = 1;
			print color("red") . "Populating only with seed data" . color("reset") . "\n";
		}
	}

	open(CONFIG, "<",	 $CRED_FILE) or die "[CONFIG] Cannot open credentials file: " . $! . "\n";
	my @lines = <CONFIG>;
	close(CONFIG);

	my ($host, $user, $pass, $dbname, $port) = ("", "", "", "", 3306);

	foreach(@lines)
	{
		next if /^[\s+]?$|^#/;
		chomp;
		if(/^user=(.*)$/) { $user = $1; }
		if(/^host=(.*)$/)
		{
			$host = $1;
		}
		if(/^port=(.*)$/) { $port = $1; }
		if(/^database=(.*)$/) { $dbname = $1; }
		if(/^password=(.*)$/) { $pass = $1; }
		if(/^mysqlcmd=(.*)$/) { $MYSQL_CMD = $1; }
	}

	die "[CONFIG] Credentials file ($CRED_FILE) does not have required properties " .
		"(host,user,pass,db name,port)" 
		if $host eq "" || $user eq "" || $pass eq "" || $dbname eq "" || $port eq "";

	my @sql_dbs = ();
	my @sql_tables = ();

	find { wanted => sub {push @sql_tables, $_ if /\.sql$/}}, "./schemas";	
	find { wanted => sub {push @sql_dbs, $_ if /replication\.sql$/}}, "./databases";

	if($SEED_DATA_ONLY != 1)
	{
		if(scalar(@sql_tables) > 0)
		{
			my @hosts = split(",", $host);
			foreach my $single_host(@hosts)
			{
				if(scalar(@sql_dbs) > 0)
				{
					print color("yellow") .  "Found " . scalar(@sql_dbs) . " database drop/create scripts, execute those on host: $single_host? yes/no [yes]:" . color("reset");
					my $shard_rep_yes_no = $DEFAULT_OPTIONS || <STDIN>;

					if($shard_rep_yes_no eq "" || $shard_rep_yes_no eq "\n" || $shard_rep_yes_no =~ /y[es]?/i)
					{
						print color("cyan") . "\n[SHARDED/REPLICATION] OK, executing sharded and replication drop/create scripts" . color("reset") . "\n";
						foreach my $file(@sql_dbs)
						{
							my $cmd = $MYSQL_CMD . " -u $user --password=$pass --port=$port -h $single_host <./databases/$file";
							my $ret = system($cmd . " 2>&1");
			 
							if($? == -1)
							{
								print color("red") . "[SHARDED/REPLICATION SQL] failed to execute '$MYSQL_CMD' command: $!" . color("reset") . "\n";
							}
							elsif($ret > 0)
							{
								print color("red") . "[SHARDED/REPLICATION SQL] **** WARNING **** failed to drop/create db from file: $file, check errors" . color("reset") . "\n";
							}
							elsif($ret == 0)
							{
								print "[SHARDED/REPLICATION SQL] " . color("green") . "SUCCESS: $file" . color("reset") . "\n";
							}
						}
					}
				}

				foreach my $file(@sql_tables) 
				{
					my @dbs = split(",", $dbname);
					foreach my $single_db(@dbs)
					{
						print color("cyan") . "[TABLES SQL] Creating tables on host: [$single_host], database: [$single_db]" .color("reset") . "\n";
						my $cmd = $MYSQL_CMD . " -u $user --password=$pass --port=$port -h $single_host $single_db <./schemas/$file";
						my $ret = system($cmd . " 2>&1");

						if($? == -1)
						{
							print color("red") . "[TABLES SQL] failed to execute '$MYSQL_CMD' command: $!" . color("reset") . "\n";
						}
						elsif($ret > 0)
						{
							print color("red") . "[TABLES SQL] **** WARNING **** failed to create table from file: $file, already exists?" . color("reset") . "\n";
						}
						elsif($ret == 0)
						{
							print "[TABLES SQL] " . color("green") . "SUCCESS: $file" . color("reset") . "\n";
						}
					}
				}
			}
		}
		else
		{
			warn color("yellow") . "[TABLES SQL] No *.sql files found in ./schemas directory" . color("reset") ."\n";
		}
	}

	print "\n" . color("yellow"). "[SEED DATA] Should I populate database with seed data files? yes/no [yes]: " . color("reset");
	my $yes_no = $DEFAULT_OPTIONS || <STDIN>;

	if($yes_no eq "" || $yes_no eq "\n" || $yes_no =~ /y[es]?/i)
	{
		print color("cyan") . "\n[SEED DATA] OK, now I will populate tables with seed data, please wait..." . color("reset") . "\n";
		my @seed_data = ();
		
		find { wanted => sub {push @seed_data, $_ if /\.ddl$/}}, "./seeddata";

		if(scalar(@seed_data) > 0)
		{
			my @hosts = split(",", $host);
			foreach my $single_host(@hosts)
			{
				my @dbs = split(",", $dbname);
				foreach my $single_db(@dbs)
				{
					my $ff_table = "";
					
					foreach my $file(@seed_data)
					{
						print "\n[SEED DATA] Populating with tables on host [$single_host], database [$single_db]\n";
						my $cmd = $MYSQL_CMD . " -u $user --password=$pass --port=$port -h $single_host $single_db <./seeddata/$file";
						if($file =~ /extra.ddl/)
						{ 
							print color("yellow") . "[SEED DATA] Should I run extra ddl file ($file)? yes/no [no] : " . color("reset");
							my $extra_yes_no = "no";

							if($DEFAULT_OPTIONS ne "yes")
							{
								$extra_yes_no = <STDIN>;
							}

							if($extra_yes_no eq "" || $extra_yes_no eq "\n" || $extra_yes_no =~ /n[o]?/i)
							{
								next;
							}
						}

						my $ret = system($cmd . " 2>&1");

						if($? == -1)
						{
							print color("red") . "[SEED DATA] failed to execute '$MYSQL_CMD' command: $!" . color("reset") . "\n";
						}
						elsif($ret > 0) #|| ($? >> 8) > 0)
						{
							print color("red") . "[SEED DATA] **** WARNING ****  failed to populate seed data from: $file" . color("reset") ."\n";
						}
						else
						{
							print color("green") . "[SEED DATA] SUCCESS: $file" . color("reset"). "\n";
						}
					}
				}
			}
		}
		else
		{
			print color("yellow") . "[SEED DATA] No seed data (*.ddl) files found in ./seeddata directoy" . color("reset"). "\n";
		}
	}
	else
	{
		print "[SEED DATA] alright, not going to populate with seed data\n";
	}
}

sub trim($)
{
	my $string = shift;
	$string =~ s/^\s+//;
	$string =~ s/\s+$//;
	return $string;
}





