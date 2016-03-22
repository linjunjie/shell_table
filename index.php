<?php

error_reporting(0);

include 'shell_table.php';

$data = @include 'data.php';		//得到表格内容，请在data.php中配置

$table = new shell_table();
// $table -> setTableBorder('-');				//设置边框样式
// $table -> setTableBorderCornerStyle('+');	//设置交叉点角样式
$table -> createTable($data);
$table -> showTable();