<?php

/**
 * 	2016/03/22
 *  目的是为了打印出一个shell终端的表格，最终希望类似下面这样
 * 	得到正确的宽度值
 *	将表格的宽度值对齐
 *	可以设置表格边框和角落样式
 *
 *	@todo 表项居中
 *	@todo 表项中含有中文的话在终端中一个汉字会占用两个宽度，而copy到其他地方一个汉字会占用少于两个宽度，所以会出现显示并不对齐的问题
 *
 *	flags参数为数据收发提供了额外的控制，包括以下几个值或者互相的或
 *	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *	|     opt_name     |                          含义                          |  send  |  recv  |
 *  ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *	|                  |	指示数据链路层协议持续监听对方的回应，直到得到答复，       |       |        |
 *	|    MSG_CONFIRM   |    它仅能用于SOCK_DGRAM和SOCK_RAW类型的socket			    |	Y	|    N   |
 *  ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *	|	MSG_DONTROUTE  |        不查看路由表 		       							|	Y	|    N   |
 *  ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *	|	MSG_DONTWAIT   |                            							|	Y	|    Y   |
 *  ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *	|	MSG_MORE       |                            							|	Y	|    N   |
 *  ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *	|	MSG_WAITALL    |                            							|	N	|    Y   |
 *  ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *	|	MSG_PEEK       |                            							|	N	|    Y   |
 *  ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *	|	MSG_OOB        |                            							|	Y	|    Y   |
 *  ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *	|	MSG_NOSIGNAL   |                            							|	Y	|    N   |
 *  ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *
 */

class shell_table{

	/* 数据表项目与表格左右边界的间距 */
	private $tbl_space = '  ';

	/* 注释间距 */
	private $annotation_space = ' ';

	/* 最终生成的表格体 */
	private $tbl_body = '';

	/* 指定表格边框样式 */
	private $tbl_border_style = "-";

	/* 可以指定表格交叉点的边角样式 */
	private $tbl_border_corner_style = "+";

	/* 每一项的最大长度 */
	private $tbl_column_width_max = 30;

	public function createTable($arr){
		$body_header = $body = $body_footer = '';

		$current_item_str = '';			//当前表项中字符串内容
		$current_item_str_width = 0;	//当前表项中字符串内容占用的宽度
		$current_row_str = '';			//当前行内容
		$current_row_str_width = 0;		//当前行内容占用的宽度
		$final_row_str = '';			//最终组织成型的行内容

		$tbl_column_width = array();
		$row_prefix = '*' . $this->annotation_space;

		//第一次遍历表格所有数据以确定每一列的宽度
		foreach ($arr as $row) {
			$current_row_str = '';
			$column_index = 0;
			foreach ($row as $v) {
				$current_item_str = $this->getItem($v);		//得到此表项的字符串内容
				$current_item_str_width = $this->getStrWidth($current_item_str);	//表项的实际占用宽度
				if($tbl_column_width[$column_index] < $current_item_str_width){		//如果不是最大宽度，则保存
					$tbl_column_width[$column_index] = $current_item_str_width;		
				}
				$column_index++;
			}			
		}
		//所有使用的变量回收
		$column_index = 0;
		$current_item_str = '';
		$current_item_str_width = 0;
		$current_row_str = '';
		$current_row_str_width = 0;

		//确定了每一列的长度之后可以确定边框的样式了
		$row_width = 0;
		foreach ($tbl_column_width as $v) {		
			$row_width += $v;	//求出确定好的行的总长度
		}
		$tpl_border = str_repeat($this->tbl_border_style, $row_width);
		$tpl_border = substr_replace($tpl_border, $this->tbl_border_corner_style, 0, 1);		//第一行的边框
		foreach ($tbl_column_width as $v) {
			$column_index += $v;
			$tpl_border = substr_replace($tpl_border, $this->tbl_border_corner_style, $column_index, 1);	//中间行的边框
		}
		$column_index = 0;

		//真正的组织表格
		foreach ($arr as $row) {
			$current_row_str = '';
			$column_index = 0;
			foreach ($row as $v) {
				$current_item_str = $this->getItem($v);
				$current_item_str_width = $this->getStrWidth($current_item_str);
				$current_item_str .= str_repeat(' ', $tbl_column_width[$column_index] - $current_item_str_width);	//将表项中长度不够的位置补空格
				$current_row_str .= $current_item_str;
				$column_index++;
			}
			$current_row_str = $current_row_str . '|';
			$final_row_str = $row_prefix . $current_row_str;
			$body .= $row_prefix . $tpl_border . PHP_EOL;	//构造上边框
			$body .= $final_row_str . PHP_EOL;				//嵌入表项内容
			
		}
		$body .= $row_prefix . $tpl_border . PHP_EOL;

		$this->tbl_body .= $body_header . $body . $body_footer;
	}

	/* 得到加工之后的每一个表项 */
	function getItem($item){
		$new_item = '|';
		$new_item .= $this->tbl_space;
		$new_item .= $item;
		$new_item .= $this->tbl_space;
		return $new_item;
	}

	/**
	 * 得到字符串的实际宽度值
	 *
	 * 假设宽度单位是1kd
	 * 那么字符串中汉字的宽带大概是2kd，数字和字符的宽度大概是1kd
	 * 
	 */
	function getStrWidth($str){
		$width = 0;
		$alphanumber = 1;
		$chinese = 2;

		for($i=0; $i<strlen($str); $i++){
			if(ord(substr($str, $i, 1)) > 127){	//可能是汉字
				//如果发现时汉字，则赋予汉字宽度，并且遍历索引前进两位
				$width += $chinese;
				$i++;$i++;		//因为一个中文汉字占用三个字节，所以前进两位
			}else{
				$width += $alphanumber;
			}
		}

		return $width;
	}

	public function showTable(){
		echo $this->tbl_body;
	}

	/* 设置边框属性 */
	public function setTableBorder($style){
		$this->tbl_border_style = $style;
	}

	/* 设置表的交叉点的边角样式 */
	public function setTableBorderCornerStyle($style){
		$this->tbl_border_corner_style = $style;
	}
}


