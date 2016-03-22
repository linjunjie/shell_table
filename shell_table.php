<?php

/**
 *  目的是为了打印出一个shell终端的表格，最终希望类似下面这样	2016/03/22
 * 	得到正确的宽度值
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
 */
class shell_table{

	/* 数据表项目与表格左右边界的间距 */
	private $tbl_space = '  ';

	/* 注释间距 */
	private $annotation_space = ' ';

	/* 表格体 */
	private $tbl_body = '';

	/* 每一项的最大长度 */
	private $tbl_column_width_max = 30;

	private $tbl_column_width = 20;

	public function createTable($arr){
		$body_header = $body = $body_footer = '';
		$current_row_str = '';		//当前行内容
		$final_row_str = '';
		$row_prefix = '*' . $this->annotation_space;
		//组织表格
		foreach ($arr as $row) {
			$current_row_str = '';
			foreach ($row as $v) {
				$current_row_str .= $this->getItem($v);
			}
			$current_row_str = $current_row_str . '|';
			$final_row_str = $row_prefix . $current_row_str;
			
			$row_len = $this->getStrWidth($current_row_str);
			$body .= $row_prefix . str_repeat('+', $row_len) . PHP_EOL;
			$body .= $final_row_str . PHP_EOL;
			
		}
		$body .= $row_prefix . str_repeat('+', $row_len) . PHP_EOL;

		$this->tbl_body .= $body_header . $body . $body_footer;
	}

	/* 得到加工之后的每一项 */
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
				$i++;
				$i++;
			}else{
				$width += $alphanumber;
			}
		}

		return $width;
	}

	public function showTable(){
		echo $this->tbl_body;
	}
}

$title = array(
	array('选项名', '含义', 'send', 'recv')
);

$arr = array(
    array('MSG_CONFIRM','指示数据链路层协议持续监听对方的回应，直到得到答复，它仅能用于SOCK_DGRAM和SOCK_RAW类型的socket','Y','N'), 
    array('MSG_DONTROUTE','不看路由表，直接将数据发送给本地局域网络内的主机，这表示发送者确切地知道目标主机就在本地网络上','Y','N'), 
    array('MSG_DONTWAIT','对socket的此次操作将是非阻塞的','Y','Y'),
    array('MSG_MORE','告诉内核应用程序还有更多数据要发送，内核将超时等待新数据写入TCP发送缓冲区后一并发送，这样可以防止TCP发送过多小的报文段，从而提高传输效率','Y','N'),
    array('MSG_WAITALL','读操作仅在读取到指定数量的字节后才返回','N','Y'),
    array('MSG_PEEK','窥探缓存中的数据，此次读操作不会导致这些数据被清楚','N','Y'),
    array('MSG_OOB','发送或接收紧急数据','Y','Y'),
    array('MSG_NOSIGNAL','往读端关闭的管道或者socket接连中写数据时不引发SIGPIPE信号','Y','N'),
);

$table = new shell_table();
$table -> createTable($arr);
$table -> showTable();


