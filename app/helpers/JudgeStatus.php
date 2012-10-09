<?php
class JudgeStatus
{
  const PENDING = 0;
  const WAITING = 1;
  const RUNNING = 2;
  const DONE    = 3;
  
  static $NAMES = array(
		'Pending',	// not assigned to a judger
		'Waiting',	// already assigned to a judger (may be during compilation)
		'Running',	// running on a judge
		'Done'
	);
	
	static $CHINESE_NAMES = array(
	  '未评测',
	  '等待评测',
	  '正在评测',
	  '评测完成'
	);
}
