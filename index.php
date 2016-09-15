<?php
@set_time_limit(0);

// Get program name
$prg=basename(__FILE__);

// Determine operating system (os)
$_os='windows';
if(isset($_ENV) && (isset($_ENV['PATH']) && (substr($_ENV['PATH'], 0, 1) == '/') || (isset($_ENV['Path']) && substr($_ENV['Path'], 0, 1) == '/') || (isset($_ENV['path']) && substr($_ENV['path'], 0, 1) == '/')))
	$_os='linux';
else if((isset($_SERVER['PATH']) && (substr($_SERVER['PATH'], 0, 1) == '/')) || (isset($_SERVER['Path']) && (substr($_SERVER['Path'], 0, 1) == '/')) || (isset($_SERVER['path']) && (substr($_SERVER['path'], 0, 1) == '/')))
	$_os='linux';

// Special variables
$today=date('YmdHis');
$nl="\r\n";
$nl_f="\r\n";
if($_os != 'windows')
{
	$_oss='/';
	$nl_b="\n";
}
else
{
	$_oss='\\';
	$nl_b="\r\n";
}

// Zip Extension
$GLOBALS['zip_ext']=false;
if(extension_loaded('zip'))
	$GLOBALS['zip_ext']=true;

// Users
$users=array(
	1=>array('test','test'),
);

// Log filename + array
$tfname='call.log';
$tarray=array();
if(file_exists($tfname))
	require($tfname);

// Logged in?
$login=false;
$logvt=substr($today,0,8).'240000';
$logui=0;
if(isset($_POST) && is_array($_POST) && sizeof($_POST) && isset($_POST['lui']) && isset($_POST['lut']))
{
	$lui=(int)$_POST['lui'];
	$lut=substr(trim($_POST['lut']),4,32);
	$logut=md5('CMNG_U'.$lui.'_'.$logvt);
	if($lut === $logut)
	{
		$logui=$lui;
		$login=true;
	}
}

if(!$login && isset($_POST) && is_array($_POST) && sizeof($_POST) && isset($_POST['lbtn']))
{
	$uname='';
	if(isset($_POST) && is_array($_POST) && sizeof($_POST) && isset($_POST['luname']))
		$uname=strtolower(trim($_POST['luname']));
	$upass='';
	if(isset($_POST) && is_array($_POST) && sizeof($_POST) && isset($_POST['lupass']))
		$upass=trim($_POST['lupass']);

	foreach($users as $uid => $ua)
	{
		if(($uname === $ua[0]) && ($upass === $ua[1]))
		{
			$logui=$uid;
			$login=true;
			break;
		}
	}
}

@header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");    			// Past date
@header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT"); 	// always changed
@header("Cache-Control: no-cache, must-revalidate");  			// HTTP/1.1
@header("Pragma: no-cache");                          			// HTTP/1.0

echo('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">'.$nl);	// HTML 4.01 Strict
echo('<html>'.$nl);
echo('<head>'.$nl);
echo('	<title>CManager (Call Manager)</title>'.$nl);
echo('	<meta http-equiv="content-type" content="text/html; charset=utf-8">'.$nl);
echo('	<meta name="author" content="Walter Thaler jr.">'.$nl);
echo('	<meta name="date" content="2004-01-01T12:00:00+00:00">'.$nl);
echo('	<meta name="robots" content="no follow">'.$nl);
echo('	<style type="text/css">'.$nl);
echo('		* {font-family:verdana,arial,helvetica,sans-serif;font-size:12px;}'.$nl);
echo('		html {scrollbar-face-color:#eeeff3;scrollbar-highlight-color:#ffffff;scrollbar-3dlight-color:#cccccc;scrollbar-shadow-color:#aaaaaa;scrollbar-darkshadow-color:#444444;scrollbar-arrow-color:#000022;scrollbar-track-color:#e8e9f0;}'.$ml);
echo('		body {background:#bbbbbb;z-index:0;color:#000000;scrollbar-face-color:#eeeff3;scrollbar-highlight-color:#ffffff;scrollbar-3dlight-color:#cccccc;scrollbar-shadow-color:#aaaaaa;scrollbar-darkshadow-color:#444444;scrollbar-arrow-color:#000022;scrollbar-track-color:#e8e9f0;}'.$nl);
echo('		.big {font-size:14px;}'.$nl);
echo('		.itext {background:#eeeeee;border:0;border-left:1px solid #000000;border-top:1px solid #000000;}'.$nl);
echo('	</style>'.$nl);
echo("	<script type=\"text/javascript\">
var settime=0;
Timer=setInterval('adjustTime()', 1000);
function adjustTime()
{
	if(settime)
	{
		var el=document.getElementById('detime');
		if(el)
		{
			var adate=new Date();
			var adh=adate.getHours();
			var adi=adate.getMinutes();

			el.value=('0'+adh).substr(-2)+':'+('0'+adi).substr(-2);
		}
	}
}
function openData(id)
{
	var adate=new Date();

	var add=adate.getDate();
	var adm=adate.getMonth()+1;
	var ady=adate.getFullYear();

	var adh=adate.getHours();
	var adi=adate.getMinutes();

	var awd=adate.getDay();

	var data=document.getElementById('data');
	var da=['date','stime','etime','mode','tel','status','company','person','cause','note'];
	for(var d=0; d<da.length; d++)
	{
		if(id.length)
		{
			var sel=document.getElementById(da[d]+'_'+id);
			var del=document.getElementById('d'+da[d]);
			del.value=sel.innerHTML;
		}
		else
		{
			var del=document.getElementById('d'+da[d]);
			if(da[d] == 'mode')
				del.value='in';
			else if(da[d] == 'date')
				del.value=ady+'-'+('0'+adm).substr(-2)+'-'+('0'+add).substr(-2);
			else if((da[d] == 'stime') || (da[d] == 'etime'))
				del.value=('0'+adh).substr(-2)+':'+('0'+adi).substr(-2);
			else if(da[d] == 'status')
				del.value='Recorded';
			else
				del.value='';
		}
	}

	toggleCallMode(0);

	var btn=document.getElementById('btn');
	if(id.length)
	{
		btn.value='Save changed call';
		settime=0;
	}
	else
	{
		btn.value='Save new call';
		settime=1;
		adjustTime();
	}

	var eid=document.getElementById('id');
	eid.value=id;

	data.style.display='';
}
function closeData()
{
	var data=document.getElementById('data');
	data.style.display='none';
	settime=0;
}
function saveData()
{
	closeData();
	var df=document.getElementById('workform');
	df.submit();
}
function dateChanged()
{
	closeData();
	var df=document.getElementById('dateform');
	df.submit();
}
function changeCall(id)
{
	closeData();
	openData(id);
}
function deleteCall(id)
{
	closeData();
	var del=document.getElementById('del');
	if(del)
	{
		var cd=id.substr(0,4)+'-'+id.substr(4,2)+'-'+id.substr(6,2)+', '+id.substr(8,2)+':'+id.substr(10,2);
		var r=confirm('Really delete this call from ['+cd+']?');
		if(r == true)
		{
			del.value=id;
			var df=document.getElementById('dateform');
			df.submit();
		}
	}
}
function toggleCallMode(toggle)
{
	var mode=document.getElementById('dmode');
	if(toggle)
	{
		if(mode.value == 'in')
			mode.value='out';
		else
			mode.value='in';
	}

	var mt=document.getElementById('dmodetxt');
	if(mode.value == 'in')
	{
		mt.style.color='#006b9f';
		mt.innerHTML='[Incoming Call]';
	}
	else
	{
		mt.style.color='#009f6b';
		mt.innerHTML='[Outgoing Call]';
	}

	var mi=document.getElementById('dmodeimg');
	mi.src='ic'+mode.value+'.png';
}
function login()
{
	var lf=document.getElementById('loginform');
	lf.submit();
}
	</script>".$nl);
echo('</head>'.$nl);
echo('<body style="margin:0;padding:0;overflow:hidden;">'.$nl);

$uname='';
if($login && $logui)
	$uname=' <i class="big" style="color:#000000;">['.$users[$logui][0].']</i>';

echo('<div style="position:fixed;left:0;top:0;right:0;bottom:0;background:#bbbbbb;background:-webkit-linear-gradient(top,#dddddd,#ffffff);background:linear-gradient(to bottom,#dddddd,#ffffff);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#dddddd\',endColorstr=\'#ffffff\',GradientType=0);"></div>'.$nl);

echo('	<div style="position:fixed;left:0;top:0;right:0;height:24px;background:#006b9f;border-bottom:2px solid #bbbbbb;color:#ffffff;padding:8px;font-size:14pt;font-weight:bold;">CManager'.$uname.'</div>'.$nl);
echo('	<div style="position:fixed;right:0;top:0;color:#000000;padding:10px;font-size:11pt;font-weight:bold;">FeRox Management Consulting GmbH</div>'.$nl);

if($login)	// Login OK
{
	$logut=chr(rand(97,102)).chr(rand(97,102)).chr(rand(48,57)).chr(rand(97,102)).md5('CMNG_U'.$logui.'_'.$logvt).chr(rand(97,102)).chr(rand(48,57)).chr(rand(97,102)).chr(rand(97,102)).chr(rand(48,57)).chr(rand(97,102)).chr(rand(48,57));
	$hid='<input type="hidden" name="lui" value="'.$logui.'"><input type="hidden" name="lut" value="'.$logut.'">';

	// Timespan
	$d=(int)substr($today,6,2);
	$m=(int)substr($today,4,2);
	$y=(int)substr($today,0,4);
	$edate=$y.'-'.substr('0'.$m,-2).'-'.substr('0'.$d,-2);
	$sdate=date('Y-m-d', mktime(12,0,0, $m,$d-14,$y));
	if(isset($_POST) && is_array($_POST) && sizeof($_POST) && isset($_POST['sdate']))
	{
		$sdate=trim($_POST['sdate']);
		$edate=trim($_POST['edate']);
	}
	$tsdate='00000000';
	if(strlen($sdate))
		$tsdate=substr($sdate,0,4).substr($sdate,5,2).substr($sdate,8,2);
	$tedate='99999999';
	if(strlen($edate))
		$tedate=substr($edate,0,4).substr($edate,5,2).substr($edate,8,2);
	if($tedate < $tsdate)
	{
		$tedate=$tsdate;
		if(strlen($edate))
			$edate=substr($tedate,0,4).'-'.substr($tedate,4,2).'-'.substr($tedate,6,2);
	}

	$entry=array();

	// Delete
	$del='';
	if(isset($_POST) && is_array($_POST) && sizeof($_POST) && isset($_POST['del']))
	{
		$del=trim($_POST['del']);
		$entry=array('id'=>$del, 'delete'=>true);
	}

	// Data
	$id='';
	$dmode='in';
	$ddate=substr($today,0,4).'-'.substr($today,4,2).'-'.substr($today,6,2);
	$dstime=substr($today,8,2).':'.substr($today,10,2);
	$detime=$dstime;
	$dtel='';
	$dstatus='';
	$dcompany='';
	$dperson='';
	$dcause='';
	$dnote='';
	if(isset($_POST) && is_array($_POST) && sizeof($_POST) && isset($_POST['ddate']))
	{
		$id=trim($_POST['id']);

		$ddate=trim($_POST['ddate']);
		$dstime=trim($_POST['dstime']);
		$detime=trim($_POST['detime']);
		$dmode=trim($_POST['dmode']);

		$dtel=trim($_POST['dtel']);
		$dstatus=trim($_POST['dstatus']);

		$dcompany=trim($_POST['dcompany']);
		$dperson=trim($_POST['dperson']);

		$dcause=trim($_POST['dcause']);
		$dnote=trim($_POST['dnote']);

		if(strlen($ddate))
		{
			$tddate=substr($ddate,0,4).substr($ddate,5,2).substr($ddate,8,2);
			if($tedate < $tddate)
			{
				$tedate=$tddate;
				$edate=substr($tedate,0,4).'-'.substr($tedate,4,2).'-'.substr($tedate,6,2);
			}
			if($tsdate > $tddate)
			{
				$tsdate=$tddate;
				$sdate=substr($tsdate,0,4).'-'.substr($tsdate,4,2).'-'.substr($tsdate,6,2);
			}

			// Insert entry
			$entry=array('id'=>$id, 'delete'=>false, 'date'=>$ddate, 'stime'=>$dstime, 'etime'=>$detime, 'mode'=>$dmode, 'tel'=>$dtel, 'status'=>$dstatus, 'company'=>$dcompany, 'person'=>$dperson, 'cause'=>$dcause, 'note'=>$dnote);
		}
	}

	// Adjust entries
	$save=false;
	if(sizeof($entry))
	{
		$save=true;
		$narray=array();
		$elog='';
		if(sizeof($tarray))
		{
			foreach($tarray as $tid => $tdata)
			{
				if($tid != $entry['id'])
					$narray[$tid]=$tdata;
				else
					$elog=$tdata['log'];
			}
		}
		if(!$entry['delete'])
		{
			$sta=explode(':',$entry['stime']);
			$nid=substr($entry['date'],0,4).substr($entry['date'],5,2).substr($entry['date'],8,2).substr('00'.$sta[0],-2);
			if(sizeof($sta) > 1)
				$nid .= substr('00'.$sta[1],-2);
			else
				$nid .= '00';

			$narray[$nid]=$entry;
			unset($narray[$nid]['id']);
			unset($narray[$nid]['delete']);

			if(strlen($elog))
				$elog .= '|';
			$narray[$nid]['log']=$elog.$today.','.$logui;
		}

		if(sizeof($narray))
			krsort($narray);
		$tarray=$narray;
	}

	// Save log
	if($save)
	{
		if(file_exists($tfname) && !sizeof($tarray))
		{
			@chmod($tfname, 0777);
			@unlink($tfname);
		}
		else
		{
			$fp=@fopen($tfname, 'w+');
			if($fp)
			{
				$cnt=1;
				$soa=sizeof($tarray);
				$t="<"."?"."php \$tarray=array(".$nl_b;
				foreach($tarray as $tid => $tdata)
				{
					$t .= "\t'".$tid."' => array(";
					foreach($tdata as $tvar => $tval)
					{
						$t .= "'".$tvar."'=>";
						if(strlen($tval))
							$t .= "'".str_replace("'", "\\\'", $tval)."'";
						else
							$t .= "''";
						if($tvar != 'log')
							$t .= ", ";
					}
					$t .= ")";
					if($cnt < $soa)
						$t .= ",";
					$t .= $nl_b;

					$cnt++;
				}
				$t .= "); ?".">";

				$bw=fwrite($fp,$t);
				fclose($fp);
			}
		}
	}

	// Filter entries
	$farray=array();
	if(sizeof($tarray))
	{
		foreach($tarray as $tid => $tdata)
		{
			$c=substr($tid,0,8);
			if(($c >= $tsdate) && ($c <= $tedate))
				$farray[$tid]=$tdata;
		}
	}

	// List (Start- + End date)
	$w1=150;
	$w2=70;
	$w3=160;
	$w4=210;
	$w5=210;
	$w6=400;
	$w7=200;
	$hbg='bbbbbb';
	echo('		<div style="position:absolute;top:48px;bottom:8px;left:8px;right:8px;line-height:19px;border:1px solid #888888;border-radius:6px;background:#e4e4e4;box-shadow:2px 2px 4px #888888;overflow:hidden;">'.$nl);
	echo('			<div style="background:#888888;padding:4px 8px;">'.$nl);
	echo('				<form id="dateform" action="'.$prg.'" method="post">'.$nl);
	echo('					<b class="big" style="color:#ffffff;">Call list:</b>&nbsp;&nbsp;');
	echo('					<input class="itext big" id="sdate" name="sdate" type="text" value="'.$sdate.'" style="width:90px;text-align:center;" onchange="dateChanged();">');
	echo('					<b class="big" style="color:#ffffff;">-</b> <input class="itext big" id="edate" name="edate" type="text" value="'.$edate.'" style="width:90px;text-align:center;" onchange="dateChanged();">'.$nl);
	echo('					<input id="del" name="del" type="hidden" value="">'.$hid.$nl);
	echo('					<span class="big" style="color:#ffffff;position:absolute;right:6px;cursor:pointer;" onclick="openData(\'\');"><img src="icne.png" align="top">&nbsp; Register new call</span>'.$nl);
	if(sizeof($tarray))
	{
		$fc='b1001a';
		if(sizeof($farray))
			$fc='006f3b';
		$als='<b class="big" style="color:#'.$fc.';">'.sizeof($farray).'</b>/'.sizeof($tarray).' ';
		if(sizeof($tarray) == 1)
			$als .= 'entry';
		else
			$als .= 'entries';
		echo('					<i class="big" style="margin-left:16px;">('.$als.')</i>'.$nl);
	}
	echo('				</form>'.$nl);
	echo('			</div>'.$nl);

	if(sizeof($farray))
	{
		echo('			<div style="position:absolute;top:36px;left:8px;right:8px;line-height:19px;border:0;overflow:none;">'.$nl);
		echo('				<table border="0" cellspacing="0" cellpadding="0">'.$nl);
		echo('					<tr>'.$nl);
		echo('						<td style="background:#'.$hbg.';border-right:1px solid #e4e4e4;width:'.$w1.'px;padding:2px 4px;"><b>Date/Time</b></td>'.$nl);
		echo('						<td style="background:#'.$hbg.';border-right:1px solid #e4e4e4;width:'.$w2.'px;padding:2px 4px;"><b>Mode</b></td>'.$nl);
		echo('						<td style="background:#'.$hbg.';border-right:1px solid #e4e4e4;width:'.$w3.'px;padding:2px 4px;"><b>Tel. Number</b></td>'.$nl);
		echo('						<td style="background:#'.$hbg.';border-right:1px solid #e4e4e4;width:'.$w4.'px;padding:2px 4px;"><b>Company</b></td>'.$nl);
		echo('						<td style="background:#'.$hbg.';border-right:1px solid #e4e4e4;width:'.$w5.'px;padding:2px 4px;"><b>Contact</b></td>'.$nl);
		echo('						<td style="background:#'.$hbg.';border-right:1px solid #e4e4e4;width:'.$w6.'px;padding:2px 4px;"><b>Cause</b></td>'.$nl);
		echo('						<td style="background:#'.$hbg.';border-right:1px solid #e4e4e4;width:'.$w7.'px;padding:2px 4px;"><b>Status</b></td>'.$nl);
		echo('					</tr>'.$nl);
		echo('				</table>'.$nl);
		echo('			</div>'.$nl);
	}

	echo('			<div style="position:absolute;top:60px;bottom:8px;left:8px;right:8px;line-height:19px;border:0;overflow:auto;">'.$nl);

/*
	if(isset($_POST) && is_array($_POST) && sizeof($_POST))
	{
		echo('<b>$_POST</b><br />');print_r($_POST);echo('<hr />');
	}
	echo('$tsdate='.$tsdate.', $tedate='.$tedate.'<hr />');
	echo('$del='.$del.'<hr />');
	echo('<b>$entry</b><br />');print_r($entry);echo('<hr />');
	echo('<b>$tarray</b><br />');print_r($tarray);echo('<hr />');
*/

	if(sizeof($farray))
	{
		echo('				<table border="0" cellspacing="0" cellpadding="0">'.$nl);
		foreach($farray as $tid => $tdata)
		{
			$hinf='';
			foreach($tdata as $tvar => $tval)
				$hinf .='<div id="'.$tvar.'_'.$tid.'" style="display:none;">'.$tval.'</div>';

			$cdatetime='<b>'.$tdata['date'].'</b>, '.$tdata['stime'];
			if($tdata['etime'] != $tdata['stime'])
				$cdatetime .= '-'.$tdata['etime'];

			if($tdata['mode'] == 'in')
				$mc='79aec7';
			else
				$mc='79c7ae';

			echo('					<tr>'.$nl);
			echo('						<td nowrap valign="top" style="background:#d4d4d4;border-right:1px solid #e4e4e4;width:'.$w1.'px;padding:2px 4px;">'.$cdatetime.'</td>'.$nl);
			echo('						<td valign="top" style="background:#'.$mc.';border-right:1px solid #e4e4e4;width:'.$w2.'px;padding:2px 4px;"><img src="ic'.$tdata['mode'].'.png" height="22" align="top">&nbsp;&nbsp;<i>['.ucfirst($tdata['mode']).']</i></td>'.$nl);
			echo('						<td valign="top" style="background:#f8f8f8;border-right:1px solid #e4e4e4;width:'.$w3.'px;padding:2px 4px;">'.$tdata['tel'].'</td>'.$nl);
			echo('						<td valign="top" style="background:#f8f8f8;border-right:1px solid #e4e4e4;width:'.$w4.'px;padding:2px 4px;">'.$tdata['company'].'</td>'.$nl);
			echo('						<td valign="top" style="background:#f8f8f8;border-right:1px solid #e4e4e4;width:'.$w5.'px;padding:2px 4px;">'.$tdata['person'].'</td>'.$nl);
			echo('						<td valign="top" style="background:#f8f8f8;border-right:1px solid #e4e4e4;width:'.$w6.'px;padding:2px 4px;">'.$tdata['cause'].'</td>'.$nl);
			echo('						<td valign="top" style="background:#f8f8f8;border-right:1px solid #e4e4e4;width:'.$w7.'px;padding:2px 4px;">'.$tdata['status'].'</td>'.$nl);
			echo('						<td valign="top" style="padding:2px 4px;"><img src="icch.png" title="Edit call" style="margin-left:2px;cursor:pointer;" onclick="changeCall(\''.$tid.'\');"><img src="icde.png" title="Delete call" style="margin-left:4px;cursor:pointer;" onclick="deleteCall(\''.$tid.'\');">'.$hinf.'</td>'.$nl);
			echo('					</tr>'.$nl);
			echo('					<tr>'.$nl);
			echo('						<td colspan="7" style="border-top:1px solid #cccccc;height:1px;"></td>'.$nl);
			echo('						<td></td>'.$nl);
			echo('					</tr>'.$nl);
		}
		echo('				</table>'.$nl);
	}
	else if(sizeof($tarray))
		echo('				<i class="big" style="color:#e1001a;margin-left:16px;">No entries found in this timeframe!</i><br />'.$nl);
	else
		echo('				<i class="big" style="color:#006b9f;margin-left:16px;">There are no entries yet!</i><br />'.$nl);
	echo('			</div>'.$nl);
	echo('		</div>'.$nl);

	// Entry
	$w1=130;
	$w2=180;

	echo('		<div id="data" style="position:absolute;top:15%;left:22%;width:1050px;height:550px;border:1px solid #888888;border-radius:6px;background:#e4e4e4;box-shadow:6px 6px 12px rgba(0,0,0,0.5);overflow:hidden;display:none;">'.$nl);
	echo('			<div style="background:#888888;padding:4px 8px;"><b class="big" style="color:#ffffff;">Call data:</b><img src="iccl.png" title="Close" style="position:absolute;top:2px;right:6px;cursor:pointer;" onclick="closeData();"></div>'.$nl);
	echo('			<form id="workform" action="'.$prg.'" method="post">'.$nl);
	echo('				<div style="float:left;width:'.$w1.'px;padding:16px 0 0 8px;"><b class="big">Date:</b></div><input class="itext big" id="ddate" name="ddate" type="text" value="'.$ddate.'" style="float:left;margin-top:16px;width:90px;text-align:center;">'.$nl);
	echo('				<div style="float:left;width:'.$w1.'px;padding:16px 8px 0 0;text-align:right;"><b class="big">Time:</b></div><input class="itext big" id="dstime" name="dstime" type="text" value="'.$dstime.'" style="float:left;margin-top:16px;width:50px;text-align:center;">'.$nl);
	echo('				<div style="float:left;padding:16px 6px 0 6px;text-align:right;"><b class="big">-</b></div><input class="itext big" id="detime" name="detime" type="text" value="'.$detime.'" style="float:left;margin-top:16px;width:50px;text-align:center;">'.$nl);
	echo('				<div style="float:left;width:'.$w2.'px;padding:16px 8px 0 0;text-align:right;"><b class="big">Mode:</b></div><div style="padding-top:16px;" title="Change mode" style="cursor:pointer;" onclick="toggleCallMode(1);"><i id="dmodetxt" class="big" style="color:#888888;cursor:pointer;"></i>&nbsp;&nbsp;<img id="dmodeimg" src="icin.png" height="21" align="top" style="cursor:pointer;"></div>'.$nl);

	echo('				<div style="width:100%;height:24px;">&nbsp;</div>');

	echo('				<div style="float:left;width:'.$w1.'px;padding-left:8px;"><b class="big">Tel. No:</b></div><input class="itext big" id="dtel" name="dtel" type="text" value="'.$dtel.'" style="float:left;width:354px;">'.$nl);
	echo('				<div style="float:left;width:'.$w2.'px;padding-right:8px;text-align:right;"><b class="big">Status:</b></div><input class="itext big" id="dstatus" name="dstatus" type="text" value="'.$dstatus.'" style="width:354px;">'.$nl);

	echo('				<div style="width:100%;height:24px;">&nbsp;</div>');

	echo('				<div style="float:left;width:'.$w1.'px;padding-left:8px;"><b class="big">Company:</b></div><input class="itext big" id="dcompany" name="dcompany" type="text" value="'.$dcompany.'" style="float:left;width:354px;">'.$nl);
	echo('				<div style="float:left;width:'.$w2.'px;padding-right:8px;text-align:right;"><b class="big">Contact:</b></div><input class="itext big" id="dperson" name="dperson" type="text" value="'.$dperson.'" style="width:354px;">'.$nl);

	echo('				<div style="width:100%;height:12px;">&nbsp;</div>');

	echo('				<div style="float:left;width:'.$w1.'px;padding-left:8px;"><b class="big">Cause:</b></div><input class="itext big" id="dcause" name="dcause" type="text" value="'.$dcause.'" style="width:900px;">'.$nl);

	echo('				<div style="width:100%;height:24px;">&nbsp;</div>');

	echo('				<div style="float:left;width:'.$w1.'px;padding-left:8px;"><b class="big">Note:</b></div><textarea class="itext big" id="dnote" name="dnote" style="width:900px;height:200px;">'.$dnote.'</textarea>'.$nl);

	echo('				<input id="id" name="id" type="hidden" value="'.$id.'"><input id="dmode" name="dmode" type="hidden" value="'.$dmode.'"><input name="sdate" type="hidden" value="'.$sdate.'"><input name="edate" type="hidden" value="'.$edate.'">'.$hid.$nl);

	echo('				<div style="position:absolute;left:0;right:0;bottom:24px;text-align:center;"><input id="btn" name="btn" type="button" value="" style="font-size:20px;padding:4px 12px;box-shadow:2px 2px 6px rgba(0,0,0,0.5);" onclick="saveData();"></div>'.$nl);
	echo('			</form>'.$nl);
	echo('		</div>'.$nl);
}
else
{
	$w1=130;

	$uname='';
	if(isset($_POST) && is_array($_POST) && sizeof($_POST) && isset($_POST['luname']))
		$uname=trim($_POST['luname']);

	$mcol='888888';
	if(isset($_POST) && is_array($_POST) && sizeof($_POST) && isset($_POST['lbtn']))
		$mcol='e1001a';

	echo('		<div style="position:absolute;top:40%;left:38%;width:450px;height:220px;line-height:19px;border:1px solid #'.$mcol.';border-radius:6px;background:#e4e4e4;box-shadow:2px 2px 4px #888888;overflow:hidden;">'.$nl);
	echo('			<div style="background:#'.$mcol.';padding:4px 8px;"><b class="big" style="color:#ffffff;">Login:</b></div>'.$nl);
	echo('			<form id="loginform" action="'.$prg.'" method="post">'.$nl);
	echo('				<div style="float:left;width:'.$w1.'px;padding:26px 0 0 28px;"><b class="big">Name:</b></div><input class="itext big" id="luname" name="luname" type="text" value="'.$uname.'" style="margin-top:26px;width:256px;">'.$nl);
	echo('				<div style="width:100%;height:12px;">&nbsp;</div>');
	echo('				<div style="float:left;width:'.$w1.'px;padding-left:28px;"><b class="big">Password:</b></div><input class="itext big" id="lupass" name="lupass" type="password" value="" style="width:256px;">'.$nl);
	echo('				<div style="position:absolute;left:0;right:0;bottom:24px;text-align:center;"><input id="lbtn" name="lbtn" type="submit" value="Login" style="font-size:20px;padding:4px 12px;box-shadow:2px 2px 6px rgba(0,0,0,0.5);" onclick="login();"></div>'.$nl);
	echo('			</form>'.$nl);
	echo('		</div>'.$nl);
}

echo('</body>'.$nl);
echo('</html>');
?>