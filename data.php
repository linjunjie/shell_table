<?php

//标题
$title = array(
	array('选项名', '含义', 'send', 'recv')
);

//正文
$data = array(
    array('MSG_CONFIRM','指示数据链路层协议持续监听对方的回应，直到得到答复，它仅能用于SOCK_DGRAM和SOCK_RAW类型的socket','Y','N'), 
    array('MSG_DONTROUTE','不看路由表，直接将数据发送给本地局域网络内的主机，这表示发送者确切地知道目标主机就在本地网络上','Y','N'), 
    array('MSG_DONTWAIT','对socket的此次操作将是非阻塞的','Y','Y'),
    array('MSG_MORE','告诉内核应用程序还有更多数据要发送，内核将超时等待新数据写入TCP发送缓冲区后一并发送，这样可以防止TCP发送过多小的报文段，从而提高传输效率','Y','N'),
    array('MSG_WAITALL','读操作仅在读取到指定数量的字节后才返回','N','Y'),
    array('MSG_PEEK','窥探缓存中的数据，此次读操作不会导致这些数据被清楚','N','Y'),
    array('MSG_OOB','发送或接收紧急数据','Y','Y'),
    array('MSG_NOSIGNAL','往读端关闭的管道或者socket接连中写数据时不引发SIGPIPE信号','Y','N'),
);

return array_merge($title, $data);