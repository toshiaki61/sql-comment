SELECT
	*
FROM t_test_test
/*BEGIN*/
WHERE /*test*/'' AND 1=1
	/*IF aaa != ''*/AND aaa = /*aaa*/''/*END*/
/*END*/