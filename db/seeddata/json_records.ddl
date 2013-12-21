/* DELETE FROM json_records WHERE id IN(0, 1); */

INSERT IGNORE INTO json_records VALUES
(0, "Name0", "Value0", CURRENT_TIMESTAMP()),
(1, "name1", "Value1", CURRENT_TIMESTAMP())
;
