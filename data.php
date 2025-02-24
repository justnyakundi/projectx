<?php include('db_fns.php');
if(isset($_SESSION['valid_user'])){
$username=$_SESSION['valid_user'];
$result =mysqli_query($db,"select * from users where name='".$username."'");
$row=mysqli_fetch_array($result);
$usertype=stripslashes($row['position']);
$userid=stripslashes($row['userid']);
$userdep=stripslashes($row['dept']);
include('functions.php'); 
}
else{echo"<script>window.location.href = \"index.php\";</script>";}
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="js/functions.js"></script>
<script>window.onload = setupRefresh</script>
<script type="text/javascript" src="js/script.js"></script>
<link rel="stylesheet" type="text/css" href="css/graph.css" />
<script type="text/javascript" src="js/jquery.flot.min.js"></script>
<script type="text/javascript" src="js/graph.js"></script>


<?php 
$id=$_GET['id'];
function chat(){
	echo'<div id="scrollbar2" style="width:280px; height:90px" ondblclick="refreshBlock();" onmouseenter="scroller2();" title="Double Click to Refresh">
		<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
		<div class="viewport" style="width:260px; height:90px">
		<div class="overview">';
								$date=date('d/m/y');
								$result =mysql_query($db,"select * from chat where date='".$date."' order by serial desc");
								
								$num_results = mysqli_num_rows($result);
									for ($a=0; $a <$num_results; $a++) {
										$row=mysqli_fetch_array($result);
										$name=stripslashes($row['name']);
										$chat=stripslashes($row['chat']);
						echo"<p style=\"font-size:12px; color:#333; margin:0px 5px; cursor:pointer;\">";
							echo"<img src=\"images/bullet.png\" style=\"width:12px; height:12px; margin:0 3px -2px 0\" /><strong style=\"color:#0085b2\">".$name."</strong>-".$chat."</p>";
									}
								echo'</div></div></div>';
								
			
				
											
}
function taska(){ 
	//check for tasks
		$result =mysqli_query($db,"select * from mytasks where ReminderStatus=1 and Stamp<=".date('YmdHi')." order by Event_id asc limit 0,1");
				$num_results = mysqli_num_rows($result);
				if($num_results>0){
				$row=mysqli_fetch_array($result);
				$eid=stripslashes($row['Event_id']);
				$reason=stripslashes($row['Reason']);
				echo"<script>$('#mytask').html('<embed src=\"images/beep-05.wav\" autostart=\"true\" width=\"0\" height=\"0\" id=\"sound9\" enablejavascript=\"\">');</script>";	
				echo"<script>taskrem(".$eid.",'".$reason."','".stripslashes($row['DueDate'])."','".stripslashes($row['DueTime'])."');</script>";
				
				$result = mysqli_query($db,"update mytasks set ReminderStatus=0 where Event_id='".$eid."'");
				}
	//check for appointments
	
	
	$result =mysqli_query($db,"select * from calendar where ReminderStatus=1 and Stamp<=".date('YmdHi')." order by Event_id asc limit 0,1");
				$num_results = mysqli_num_rows($result);
				if($num_results>0){
				$row=mysqli_fetch_array($result);
				$eid=stripslashes($row['Event_id']);
				$reason=stripslashes($row['Reason']);
				echo"<script>$('#mycalendar').html('<embed src=\"images/beep-05.wav\" autostart=\"true\" width=\"0\" height=\"0\" id=\"sound9\" enablejavascript=\"\">');</script>";	
				echo"<script>taskcal(".$eid.",'".$reason."','".stripslashes($row['StartDate'])."','".stripslashes($row['StartTime'])."','".stripslashes($row['Pat_name'])."');</script>";
				
				$result = mysqli_query($db,"update calendar set ReminderStatus=0 where Event_id='".$eid."'");
				}			
				

}

switch($id){
							
							case 1:
							$cid=$_GET['cid'];
							$cname=$_GET['cname'];
							$paidam=$amount=$_GET['amount'];
							$change=$_GET['change'];
							$pid=$_GET['pid'];
							$pname=$_GET['pname'];
							$fintot=$_GET['fintot'];
							$debtor=$_GET['debtor'];
							$date=$_GET['date'];
							$stype=$_GET['type'];
							$stamp=preg_replace('~/~', '', $date);
							$max=count($_SESSION['cart']);
							$credam=$fintot-$amount;
							
			//get receipt no and insert into sales
			$question =mysqli_query($db,"SELECT * FROM sales order by TransNo desc limit 0,1");
			$ans=mysqli_fetch_array($question);
			$rcptno=stripslashes($ans['RcptNo'])+1;
			
					$string='';$totgoods=0;
					for ($i = 0; $i < $max; $i++){
							$itcode = $_SESSION['cart'][$i][0];
							$itname = $_SESSION['cart'][$i][1];
							$itquat = $_SESSION['cart'][$i][2];
							$itprice = $_SESSION['cart'][$i][3];
							$tprice = $_SESSION['cart'][$i][4];
							$tvat = $_SESSION['cart'][$i][5];
							$tdisc = $_SESSION['cart'][$i][6];
							$ftotal = $_SESSION['cart'][$i][7];
							$categ = $_SESSION['cart'][$i][8];
							$itcost = $_SESSION['cart'][$i][9];
							$bal = $_SESSION['cart'][$i][10];
							$qsold = $_SESSION['cart'][$i][11];
							$type = $_SESSION['cart'][$i][12];
							$qsold+=$itquat;
							
							$string.=$itname.';';
				$resulta = mysqli_query($db,"insert into sales values('','Sale','".$rcptno."','".$itcode."','".$itname."','".$itquat."','".$itprice."','".$tvat."','".$tdisc."','".$ftotal."','".$itcost."','".$date."','".date('h:i a')."','".$cid."','".$cname."','".$pid."','".$pname."','".$amount."','".$change."','".$stamp."','".$username."',1,'OTC SALES')");
			
			
						//update reduction of items
							$query =mysqli_query($db,"select * from items where ItemCode='".$itcode."'");
								$rowq=mysqli_fetch_array($query);
								$type=stripslashes($rowq['Type']);
								$pack=stripslashes($rowq['Pack']);
							if($type=='GOOD'){
								$totgoods+=$ftotal;
							//insert into stock track
		$resultd = mysqli_query($db,"insert into stocktrack values('','".date('Y/m/d')."','".$userdep."','".$itcode."','".$itname."','".$pack."','OTC SALES-RCPT No:".$rcptno."','".$itquat."','".$bal."','".$username."','".$stamp."')");	
					
					$resultb= mysqli_query($db,"update items set Bal='".$bal."',Qsold='".$qsold."' where ItemCode='".$itcode."'");
				}
			}
				
			
			//update ledgers-sales revenue
			//get balance of paymode ledger account
			$amount=$fintot;
			$resultb = mysqli_query($db,"select * from ledgers where ledgerid='".$pid."'");
			$rowb=mysqli_fetch_array($resultb);
			$recbal=stripslashes($rowb['bal']);
			$recbal=$recbal+$amount;
			
			//get balance of sales revenue ledger
			$resultz = mysqli_query($db,"select * from ledgers where ledgerid=635");
			$rowz=mysqli_fetch_array($resultz);
			$incomebal=stripslashes($rowz['bal']);
			$incomebal=$incomebal+$amount;
					
			
			$resultl = mysqli_query($db,"insert into ledgerentries values('','635','Sales Revenue','".$pid."','".$pname."','".$amount."','Income from Sales','".$date."','".$incomebal."','".$recbal."','".$stamp."',1)");
			$resultm = mysqli_query($db,"update ledgers set bal='".$incomebal."' where ledgerid='635'");
			$resultn = mysqli_query($db,"update ledgers set bal='".$recbal."' where ledgerid='".$pid."'");
			
			
			//update ledgers-inventory
			$resultb = mysqli_query($db,"select * from ledgers where ledgerid='630'");
					$rowb=mysqli_fetch_array($resultb);
					$invbal=stripslashes($rowb['bal']);
					$invbal=$invbal-$totgoods;
					
					$resultc = mysqli_query($db,"select * from ledgers where ledgerid='651'");
					$rowc=mysqli_fetch_array($resultc);
					$supbal=stripslashes($rowc['bal']);
					$supbal=$supbal-$totgoods;
					
			$resultl = mysqli_query($db,"insert into ledgerentries values('','630','Inventory','651','Supplies Revenue','".$totgoods."','Goods Sold-Rcpt No:".$rcptno."','".$date."','".$invbal."','".$supbal."','".$stamp."',1)");
			$resultm = mysqli_query($db,"update ledgers set bal='".$invbal."' where ledgerid='630'");
			$resultn = mysqli_query($db,"update ledgers set bal='".$supbal."' where ledgerid='651'");	
			
			
			
		if($pid=='628'){
		$resultc =mysqli_query($db,"SELECT * FROM creditcustomers WHERE CustomerId='".$debtor."'");
		$rowc=mysqli_fetch_array($resultc);
		$debtorname=stripslashes($rowc['CustomerName']);
		$bal2=stripslashes($rowc['Bal']);
		$bal3=$bal2+$credam;
		
		$resultd = mysqli_query($db,"insert into customerdebts values('','".$debtor."','".$debtorname."','".$rcptno."','".$fintot."','dr','".$paidam."','".$credam."','".$bal3."','".$string."','".date('Y/m/d')."','".date('Ymd')."',1)");	
		$resulte = mysqli_query($db,"update creditcustomers set Bal='".$bal3."' where CustomerId='".$debtor."'");	

				}
				if($resultb){
				unset($_SESSION['cart']);
			echo'<embed src="images/honk3.wav" autostart="true" width="0" height="0" id="sound1" enablejavascript="true">';
			echo"<script>setTimeout(function() {makeasale();},500);</script>";	
			echo"<script>$('#totitems').val('');$('#totprice').val('');$('#totvat').val('');$('#totdisc').val('');$('#fintot').val('');$('#ampaid').val('');$('#change').val('');$('#itemname').val('');$('#code').val('');$('#itcost').val('');$('#bal').val('');$('#quat').val('');$('#price').val('');$('#vat').val('');$('#tprice').val('');$('#tvat').val('');$('#disc').val('');$('#total').val('');$('#cname').val('');
			</script>";
			$resulta = mysqli_query($db,"insert into log values('','".$username." makes a sale.Rcpt No:".$rcptno."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
						echo"<script>window.open('report.php?id=1&rcptno=".$rcptno."');</script>";
						exit;
				}
									else{
								$result = mysqli_query($db,"DELETE from sales where rcptno='".$rcptno."'");
										echo"
										<script>
										$().customAlert();
										alert('Sorry!', '<p>Transaction Failure.Please Repeat the transaction.</p>');
										e.preventDefault();
										</script>";
									}
							
							break;
							
							case 2:
							
							//get receipt no and insert into sales
							$question =mysqli_query($db,"SELECT * FROM quotation order by TransNo desc limit 0,1");
							$ans=mysqli_fetch_array($question);
							$rcptno=stripslashes($ans['RcptNo'])+1;
							
							$stamp=date('Ymd');
							$dat=date('Y/m/d');
							$max=count($_SESSION['quot']);
							for ($i = 0; $i < $max; $i++){
							$itcode = $_SESSION['quot'][$i][0];
							$itname = $_SESSION['quot'][$i][1];
							$itquat = $_SESSION['quot'][$i][2];
							$itprice = $_SESSION['quot'][$i][3];
							$tprice = $_SESSION['quot'][$i][4];
							$tvat = $_SESSION['quot'][$i][5];
							$tdisc = $_SESSION['quot'][$i][6];
							$ftotal = $_SESSION['quot'][$i][7];
							$date = $_SESSION['quot'][$i][8];
							$itcost = $_SESSION['quot'][$i][9];
							$status=1;
							
						
			$resulta = mysqli_query($db,"insert into quotation values('','".$rcptno."','".$itcode."','".$itname."','".$itquat."',
							'".$itprice."','".$tvat."','".$tdisc."','".$ftotal."','".$dat."','".$status."','".$stamp."')");
			
				}
							if($resulta){
							unset($_SESSION['quot']);
			echo'<embed src="images/honk3.wav" autostart="true" width="0" height="0" id="sound1" enablejavascript="true">';
							echo"<script>$('#totitems').val('');$('#totprice').val('');$('#totvat').val('');$('#totdisc').val('');$('#fintot').val('');$('#ampaid').val('');$('#change').val('');$('#itemname').val('');$('#code').val('');$('#itcost').val('');$('#bal').val('');$('#quat').val('');$('#price').val('');$('#vat').val('');$('#tprice').val('');$('#tvat').val('');$('#disc').val('');$('#total').val('');$('#cname').val('');
							</script>";
						
										echo"
										<script>
										window.open('report.php?id=2&rcptno=".$rcptno."');
										$().customAlert();
										alert('Success!', '<p>Transaction Successful.</p>');
										e.preventDefault();
										</script>";
										exit;
									}
									else{
								$result = mysqli_query($db,"DELETE from quotation where rcptno='".$rcptno."'");
										echo"
										<script>
										$().customAlert();
										alert('Sorry!', '<p>Transaction Failure.Please Repeat the transaction.</p>');
										e.preventDefault();
										</script>";
									}
							
							break;
							
							
							case 3:
							$stamp=date('Ymd');
							$date=date('Y/m/d');
							$old=$_GET['rcptno'];
							$result =mysqli_query($db,"SELECT * FROM sales WHERE RcptNo='".$old."'");
							$count = mysqli_num_rows($result);
							if($count==0){
									echo"
										<script>
										$().customAlert();
										alert('Error!', '<p>Transaction Failure.Invalid Receipt No.</p>');
										e.preventDefault();
										</script>";
										exit;
							}
							
			//get receipt no and insert into sales
			$question =mysqli_query($db,"SELECT * FROM sales order by TransNo desc limit 0,1");
			$ans=mysqli_fetch_array($question);
			$rcptno=stripslashes($ans['RcptNo'])+1;
			
					$string='';$totgoods=0;$fintot=0;
							$max=count($_SESSION['credit']);
							for ($i = 1; $i < $max; $i++){
							$transno = $_SESSION['credit'][$i][0];
							$itname = $_SESSION['credit'][$i][1];
							$itquat = $_SESSION['credit'][$i][2];
							$itprice = $_SESSION['credit'][$i][3];
							$rquat = $_SESSION['credit'][$i][4];
							$rtot = $_SESSION['credit'][$i][5];
							$remquat = $_SESSION['credit'][$i][6];
							
						
							//get data from sales
							$query =mysqli_query($db,"select * from sales where TransNo='".$transno."'");
								$rowq=mysqli_fetch_array($query);
								$itcode=stripslashes($rowq['ItemCode']);
								$cid=stripslashes($rowq['ClientId']);
								$cname=stripslashes($rowq['ClientName']);
								$pid=stripslashes($rowq['Lid']);
								$pname=stripslashes($rowq['Lname']);
								$disc=stripslashes($rowq['Discount']);
							
							//get data from items
							$query =mysqli_query($db,"select * from items where ItemCode='".$itcode."'");
								$rowq=mysqli_fetch_array($query);
								$type=stripslashes($rowq['Type']);
								$pack=stripslashes($rowq['Pack']);
								$pprice=stripslashes($rowq['PurchPrice']);
								$vat=stripslashes($rowq['Vat']);
								$qret=stripslashes($rowq['Qret']);
								$bal=stripslashes($rowq['Bal']);
								
								//calculations
							
								$itprice=$itprice*(-1);
								$tvat=$vat*$itprice*$rquat*(0.01);
								$rtot=$rtot*(-1);
								$disc=$disc*(-1);
								$rtot=$rtot-$disc;
								$itcost=$pprice*$rquat*(-1);
								
								$fintot+=$rtot;
							
							$string.=$itname.';';
				$resulta = mysqli_query($db,"insert into sales values('','Credit','".$rcptno."','".$itcode."','".$itname."','".$rquat."','".$itprice."','".$tvat."','".$disc."','".$rtot."','".$itcost."','".$date."','".date('h:i a')."','".$cid."','".$cname."','".$pid."','".$pname."','0','0','".$stamp."','".$username."',1,'CREDIT NOTE-RECEIPT NO:".$old."')");
			
			
						//update reduction of items
							if($type=='GOOD'){
							$totgoods+=$rtot;
							$totgoods+=$disc;
							$bal=$bal+$rquat;
							$qret=$qret+$rquat;
							//insert into stock track
		$resultd = mysqli_query($db,"insert into stocktrack values('','".date('Y/m/d')."','".$userdep."','".$itcode."','".$itname."','".$pack."','CREDIT NOTE-RECEIPT NO:".$old."','".$rquat."','".$bal."','".$username."','".$stamp."')");	
					
					$resultb= mysqli_query($db,"update items set Bal='".$bal."',Qret='".$qret."' where ItemCode='".$itcode."'");
				}
			}
				
			
			//update ledgers-sales revenue
			//get balance of paymode ledger account
			$amount=$fintot;
			$resultb = mysqli_query($db,"select * from ledgers where ledgerid='".$pid."'");
			$rowb=mysqli_fetch_array($resultb);
			$recbal=stripslashes($rowb['bal']);
			$recbal=$recbal+$amount;
			
			//get balance of sales revenue ledger
			$resultz = mysqli_query($db,"select * from ledgers where ledgerid=635");
			$rowz=mysqli_fetch_array($resultz);
			$incomebal=stripslashes($rowz['bal']);
			$incomebal=$incomebal+$amount;
					
			
			$resultl = mysqli_query($db,"insert into ledgerentries values('','635','Sales Revenue','".$pid."','".$pname."','".$amount."','Income from Sales','".$date."','".$incomebal."','".$recbal."','".$stamp."',1)");
			$resultm = mysqli_query($db,"update ledgers set bal='".$incomebal."' where ledgerid='635'");
			$resultn = mysqli_query($db,"update ledgers set bal='".$recbal."' where ledgerid='".$pid."'");
			
			
			//update ledgers-inventory
			$resultb = mysqli_query($db,"select * from ledgers where ledgerid='630'");
					$rowb=mysqli_fetch_array($resultb);
					$invbal=stripslashes($rowb['bal']);
					$invbal=$invbal-$totgoods;
					
					$resultc = mysqli_query($db,"select * from ledgers where ledgerid='651'");
					$rowc=mysqli_fetch_array($resultc);
					$supbal=stripslashes($rowc['bal']);
					$supbal=$supbal-$totgoods;
					
			$resultl = mysqli_query($db,"insert into ledgerentries values('','630','Inventory','651','Supplies Revenue','".$totgoods."','Goods Sold-Rcpt No:".$rcptno."','".$date."','".$invbal."','".$supbal."','".$stamp."',1)");
			$resultm = mysqli_query($db,"update ledgers set bal='".$invbal."' where ledgerid='630'");
			$resultn = mysqli_query($db,"update ledgers set bal='".$supbal."' where ledgerid='651'");	
			
			
			
		if($pid=='628'){
		$resultc =mysqli_query($db,"SELECT * FROM customers WHERE cusno='".$cid."'");
		$rowc=mysqli_fetch_array($resultc);
		$bal2=stripslashes($rowc['bal']);
		$bal3=$bal2+$amount;
		
		$resultd = mysqli_query($db,"insert into customerdebts values('','".$cid."','".$cname."','".$rcptno."','".$amount."','dr','0','".$amount."','".$bal3."','RETURN-".$string."','".date('Y/m/d')."','".date('Ymd')."',1)");	
		$resulte = mysqli_query($db,"update customers set bal='".$bal3."' where cusno='".$cid."'");	

				}
				if($resultb){
				unset($_SESSION['credit']);
			echo'<embed src="images/honk3.wav" autostart="true" width="0" height="0" id="sound1" enablejavascript="true">';
			echo"<script>setTimeout(function() {issuecredit();},500);</script>";	
			echo"<script>$('#retotal').val('');$('#rcptno').val('');$('#cdate').val('');$('#csale').val('');</script>";
			$resulta = mysqli_query($db,"insert into log values('','".$username." makes a credit Note.Credit Note No:".$rcptno."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
						echo"<script>window.open('report.php?id=3&rcptno=".$rcptno."');</script>";
						exit;
				}
									else{
								$result = mysqli_query($db,"DELETE from sales where rcptno='".$rcptno."'");
										echo"
										<script>
										$().customAlert();
										alert('Sorry!', '<p>Transaction Failure.Please Repeat the transaction.</p>');
										e.preventDefault();
										</script>";
									}
							
							break;
							
							
							
							
							case 4:
							
							$a=$_GET['a'];
							$regn=$_GET['regn'];
							$name=strtoupper($_GET['name']);
							$oname=strtoupper($_GET['oname']);
							$lname=strtoupper($_GET['lname']);
							$dob=$_GET['dob'];
							$age=$_GET['age'];
							$pin=$_GET['pin'];
							$gender=$_GET['gender'];
							$phone=$_GET['phone'];
							$phone2=$_GET['phone2'];
							$email=$_GET['email'];
							$postal=$_GET['postal'];
							$gname=$_GET['gname'];
							$rship=$_GET['rship'];
							$gphone=$_GET['gphone'];
							$geno=$_GET['geno'];
							$gaddress=$_GET['gaddress'];
							$odetail=$_GET['odetail'];
							$stamp=date('Ymd');
							
							
							
			if($a==1){			
		$resulta = mysqli_query($db,"insert into customers values('','".$regn."','".$name."','".$oname."','".$lname."','".$dob."','".$age."','".$gender."','".$pin."','".$phone."','".$phone2."','".$postal."','".$email."','".$gname."','".$rship."','".$gphone."','".$geno."','".$gaddress."','".$odetail."',1,'".$stamp."','')");
			}else{
	$resulta = mysqli_query($db,"update customers set name='".$name."',oname='".$oname."',lname='".$lname."',dob='".$dob."',age='".$age."',gender='".$gender."',pin='".$pin."',phone='".$phone."',phone2='".$phone2."',address='".$postal."',email='".$email."',gname='".$gname."',grship='".$rship."',gcont='".$gphone."',geno='".$geno."',gaddress='".$gaddress."',odetail='".$odetail."' where cusno=".$regn."");
			}
								
							
			if($resulta){
	$resulta = mysqli_query($db,"insert into log values('','".$username." updates customers database.Customer No:".$regn."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
echo"<img src=\"images/tick.png\" style=\"\"  width=\"30\" height=\"30\"/>";
if($a==1){
echo"<script>setTimeout(function() {newcustomer();},500);</script>";	
}else{
	echo"<script>setTimeout(function() {editcustomer(21);},500);</script>";	
}
								
							}
							else {echo"<img src=\"images/delete.png\" style=\"\"  width=\"30\" height=\"30\"/>";}
							
							break;
							
							
							case 7:
								$sdate=date('Y/m/d');
								$stamp=date('Ymd');
								$pro=array();
								$max=count($_SESSION['pur']);
								for ($i = 0; $i < $max; $i++){
									$pro[]=$_SESSION['pur'][$i][0];
								}
								$pro = array_unique($pro);
								
								foreach ($pro as $key => $val) {
									for ($i = 0; $i < $max; $i++){
										$itcode = $_SESSION['pur'][$i][0];
										$itname = $_SESSION['pur'][$i][1];
										$sname = $_SESSION['pur'][$i][11];
										$itquat= $_SESSION['pur'][$i][18];
										$bal= $_SESSION['pur'][$i][10];
										$qpurch = $_SESSION['pur'][$i][15];
										$oprice = $_SESSION['pur'][$i][19];
										$saleset = $_SESSION['pur'][$i][17];
										$sp = $_SESSION['pur'][$i][4];
										$pp = $_SESSION['pur'][$i][5];
										$marg = $_SESSION['pur'][$i][20];
										$pack = $_SESSION['pur'][$i][22];
										if($val==$itcode){
											if(isset($_SESSION[$val])){
												$x=count($_SESSION[$val]);
												$_SESSION[$val][$x]=array($itquat,$bal,$qpurch,$oprice,$saleset,$sp,$marg,$pack,$itname,$sname,$pp);
											}else{
												$_SESSION[$val]=array(array());
												$_SESSION[$val][0]=array($itquat,$bal,$qpurch,$oprice,$saleset,$sp,$marg,$pack,$itname,$sname,$pp);
											}
								
										}
									}
								}
							
								foreach ($pro as $key => $val) {
										$count=count($_SESSION[$val]);
										$a=0;$b=0;$c=0;
										for ($i=0; $i <$count; $i++) {
											$a+=$_SESSION[$val][$i][0];
											$b=$_SESSION[$val][$i][1];
											$c=$_SESSION[$val][$i][2];
											$sp=$_SESSION[$val][$i][5];
											$oprice=$_SESSION[$val][$i][3];
											$saleset=$_SESSION[$val][$i][4];
											$marg=$_SESSION[$val][$i][6];
											$pack=$_SESSION[$val][$i][7];
											$itname=$_SESSION[$val][$i][8];
											$supname=$_SESSION[$val][$i][9];
											$ppr=$_SESSION[$val][$i][10];
										}
										$bal=$a+$b;
										$qpurch=$a+$c;
										//insert into stock track
										
										$resulta = mysqli_query($db,"insert into stocktrack values('','".$sdate."','PROCUREMENT','".$val."','".$itname."','".$pack."','PURCHASES-".$supname."','".$a."','".$bal."','".$username."','".$stamp."')");							
																	
																	
										$resultb= mysqli_query($db,"update items set PurchPrice='".$ppr."',Bal='".$bal."',Qpurch='".$qpurch."' where ItemCode='".$val."'");
								}
							
							
							
									$ftotal=$_GET['fintot'];
									$ftotal=preg_replace('~,~', '', $ftotal);
									
									
									//get receipt no
									$question =mysqli_query($db,"SELECT * FROM purchases order by TransNo desc limit 0,1");
									$ans=mysqli_fetch_array($question);
									$rcptno=stripslashes($ans['PurchNo'])+1;
									
									$max=count($_SESSION['pur']);
									for ($i = 0; $i < $max; $i++){
										$qpurch=0;$totalsale=0;
										$itcode = $_SESSION['pur'][$i][0];
										$itname = $_SESSION['pur'][$i][1];
										$unit1 = $_SESSION['pur'][$i][2];
										$part = $_SESSION['pur'][$i][3];
										$sprice = $_SESSION['pur'][$i][4];
										$pprice = $_SESSION['pur'][$i][5];
										$sp = $_SESSION['pur'][$i][6];
										$pp = $_SESSION['pur'][$i][7];
										$date = $_SESSION['pur'][$i][8];
										$total = $_SESSION['pur'][$i][9];
										$bal = $_SESSION['pur'][$i][10];
										$sname = $_SESSION['pur'][$i][11];
										$batch = $_SESSION['pur'][$i][12];
										$invoice = $_SESSION['pur'][$i][13];
										$expiry = $_SESSION['pur'][$i][14];
										$qpurch = $_SESSION['pur'][$i][15];
										$bonus = $_SESSION['pur'][$i][16];
										$saleset = $_SESSION['pur'][$i][17];
										$itquat = $_SESSION['pur'][$i][18];
										$sid = $_SESSION['pur'][$i][21];
										$qpurch+=$itquat;
										$exstamp=stampreverse($expiry);
										$saleprice=	round(($sp * $itquat),2);
										$totalsale+=$saleprice;
								
										$resulta = mysqli_query($db,"insert into purchases values('','".$rcptno."','".$date."','".$itcode."','".$itname."','".$unit1."','".$part."','".$itquat."','".$pprice."','".$sprice."','".$total."','".$sid."','".$sname."','".$batch."','".$expiry."','".$invoice."','".$ftotal."','".$stamp."','".$exstamp."','".$itquat."','".$date."','".$username."')");
					
									}
					
						
						
						
									if($resulta){
										//post invoice for payment
										$resultc =mysqli_query($db,"SELECT * FROM creditsuppliers WHERE CustomerId='".$sid."'");
										$rowc=mysqli_fetch_array($resultc);
										$bal2=stripslashes($rowc['Bal']);
										$bal3=$bal2+$ftotal;										
										$resulta = mysqli_query($db,"insert into supplierdebts values('','".$sid."','".$sname."','".$invoice."','".$rcptno."','".$ftotal."','dr','0','".$ftotal."','".$bal3."','Purchases','".date('d/m/Y')."','".$stamp."',1)");
										$resultn = mysqli_query($db,"update creditsuppliers set Bal='".$bal3."' where CustomerId='".$sid."'");

										//update ledgers-stock
										$amount=$totalsale;
										
										$resultc = mysqli_query($db,"select * from ledgers where ledgerid='661'");
										$rowc=mysqli_fetch_array($resultc);
										$purbal=stripslashes($rowc['bal']);
										$purbal=$purbal+$amount;

										$resulte = mysqli_query($db,"select * from ledgers where ledgerid='629'");
										$rowe=mysqli_fetch_array($resulte);
										$acbal=stripslashes($rowe['bal']);
										$acbal=$acbal+$amount;
						
										$resultl = mysqli_query($db,"insert into ledgerentries values('','629','Creditors','661','Purchases','".$amount."','Goods Received Inwards','".$date."','".$acbal."','".$purbal."','".$stamp."',0)");
										$resultm = mysqli_query($db,"update ledgers set bal='".$purbal."' where ledgerid='661'");
										$resultn = mysqli_query($db,"update ledgers set bal='".$acbal."' where ledgerid='629'");
										
										
			
										//update ledgers-acs/payable
										$amount=preg_replace('~,~', '', $ftotal);


										$resulta = mysqli_query($db,"insert into log values('','".$username." purchases stock.Id:".$rcptno."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
										unset($_SESSION['pur']);
										foreach ($pro as $key => $val) {
											unset($_SESSION[$val]);
										
										}
										
										echo'<embed src="images/honk3.wav" autostart="true" width="0" height="0" id="sound1" enablejavascript="true">';
							
							
										echo"
										<script>
										window.open('report.php?id=6&rcptno=".$rcptno."');
										setTimeout(function() {
											purchase();},500);
										$().customAlert();
										alert('Success!', '<p>Transaction Successful.</p>');
										e.preventDefault();
										</script>";
										exit;
									}else{
										$result = mysqli_query($db,"DELETE from purchases where rcptno='".$rcptno."'");
										echo"
										<script>
										$().customAlert();
										alert('Sorry!', '<p>Transaction Failure.Please Repeat the transaction.</p>');
										e.preventDefault();
										</script>";
									}
					
							break;
							
							
							case 8:
							$categ=$_GET['categ'];
							$result = mysqli_query($db,"insert into categories values('','".$categ."')");
							if($result){
							echo"<img src=\"images/tick.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>
							<script>$('#catadd').html('".$categ.";');</script>";
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							break;
							
							case 9:
							$code=$_GET['code'];
							$name=$_GET['name'];
							$pprice=$_GET['pprice'];
							$rprice=$_GET['rprice'];
							$wprice=$_GET['wprice'];
							$type=$_GET['type'];
							$mbal=$_GET['mbal'];
							$pack=$_GET['pack'];
							//$supp=$_GET['supp'];
							$quat=$_GET['quat'];
							$itemcat=$_GET['itemcat'];
							$vat=$_GET['vat'];
							$barcode=$_GET['bcode'];
							//$margin=$_GET['margin'];
							//$supid=$_GET['supid'];
							$catid=$_GET['catid'];
							$result = mysqli_query($db,"insert into items values('".$code."','".$name."','','','".$mbal."','".$rprice."','".$wprice."','".$pprice."','".$quat."','','','','','".$quat."','".$type."','".$catid."','".$itemcat."','".$vat."','','".$pack."','".$barcode."')");
							if($result){
								$resulta = mysqli_query($db,"insert into log values('','".$username." adds stock item.Item Code:".$code."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");
							echo"<img src=\"images/tick.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							echo"
										<script>
										setTimeout(function() {
											stockitems();},500);
										</script>";
							
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							break;
							
							case 10:
							$code=$_GET['code'];
							$name=$_GET['name'];
							$pprice=$_GET['pprice'];
							$rprice=$_GET['rprice'];
							$wprice=$_GET['wprice'];
							$type=$_GET['type'];
							$mbal=$_GET['mbal'];
							$pack=$_GET['pack'];
							//$supp=$_GET['supp'];
							$itemcat=$_GET['itemcat'];
							$vat=$_GET['vat'];
							$barcode=$_GET['bcode'];
							//$margin=$_GET['margin'];
							//$supid=$_GET['supid'];
							$catid=$_GET['catid'];
							$result = mysqli_query($db,"update items set ItemName='".$name."',MinBal='".$mbal."',SalePrice='".$rprice."',WholePrice='".$wprice."',PurchPrice='".$pprice."',Type='".$type."',CatId='".$catid."',Category='".$itemcat."',Vat='".$vat."',Pack='".$pack."',BarCode='".$barcode."' where ItemCode=".$code."");
							if($result){
							$resulta = mysqli_query($db,"insert into log values('','".$username." edits stock item.Item Code:".$code."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	

							echo"<img src=\"images/tick.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							echo"
										<script>
										setTimeout(function() {
											stockitems();},500);
										</script>";
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							break;
							
							case 11:
							$code=$_GET['code'];
							$result = mysqli_query($db,"DELETE from items where ItemCode='".$code."'");
							if($result){
								$resulta = mysqli_query($db,"insert into log values('','".$username." deletes stock item.Item Code:".$code."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
							echo"<img src=\"images/tick.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							echo"
										<script>
										setTimeout(function() {
											stockitems();},500);
										</script>";
							
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							break;
							
							case 12:
							//get receipt no
							$question =mysqli_query($db,"SELECT * FROM variance order by id desc limit 0,1");
							$ans=mysqli_fetch_array($question);
							$rcptno=stripslashes($ans['vno'])+1;
							
							$stamp=date('Ymd');
							$date=date('Y/m/d');
							$x='Bal';
							$max=count($_SESSION['stock']);$j=0;
							for ($i = 0; $i < $max; $i++){
							$val=$_SESSION['stock'][$i][0];
							$itname=$_SESSION['stock'][$i][1];
							$a=$_SESSION['stock'][$i][4];
							$b=$_SESSION['stock'][$i][5];
							
							$bal=$_SESSION['stock'][$i][5];
							$pack=$_SESSION['stock'][$i][7];
							$qty=$_SESSION['stock'][$i][4];

								$part=$bal%$pack;
								$unit=explode('.',($bal/$pack));
								$unit=$unit[0];
								$diffa=$qty-$bal;
								$total=$diffa*$_SESSION['stock'][$i][8];
								$j+=$total;
								$diff=$a-$b;
							
				$resultx = mysqli_query($db,"insert into variance values('','".$rcptno."','".$date."','".$val."','".$itname."','".$pack."','".$bal."','".$qty."','".$total."','".$username."','".$stamp."',1)");
				
				//insert into stock track
			$resulta = mysqli_query($db,"insert into stocktrack values('','".$date."','".$userdep."','".$val."','".$itname."','".$pack."','STOCK ADJUSTMENT','".$diff."','".$a."','".$username."','".$stamp."')");		
				
			$resultb= mysqli_query($db,"update items set ".$x."='".$a."' where ItemCode='".$val."'");
								}
				if($resultb){
					
					//update ledgers-stock
			$amount=$j;
			$resultb = mysqli_query($db,"select * from ledgers where ledgerid='630'");
					$rowb=mysqli_fetch_array($resultb);
					$invbal=stripslashes($rowb['bal']);
					$invbal=$invbal+$amount;
					
					$resultc = mysqli_query($db,"select * from ledgers where ledgerid='651'");
					$rowc=mysqli_fetch_array($resultc);
					$supbal=stripslashes($rowc['bal']);
					$supbal=$supbal+$amount;
					
			$resultl = mysqli_query($db,"insert into ledgerentries values('','651','Supplies Revenue','630','Inventory','".$amount."','Stock adjustment','".$date."','".$supbal."','".$invbal."','".$stamp."','','',0)");
			$resultm = mysqli_query($db,"update ledgers set bal='".$invbal."' where ledgerid='630'");
			$resultn = mysqli_query($db,"update ledgers set bal='".$supbal."' where ledgerid='651'");
					
					
$resulta = mysqli_query($db,"insert into log values('','".$username." makes stock adjustment','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
						echo'<embed src="images/honk3.wav" autostart="true" width="0" height="0" id="sound1" enablejavascript="true">';	
					echo"<script>		
										window.open('output.php?id=12');
										window.open('report.php?id=50&rcptno=".$rcptno."');
										setTimeout(function() {
										stockadj();},500);
										$().customAlert();
										alert('Success!', '<p>Transaction Successful.</p>');
										e.preventDefault();
										</script>";
										unset($_SESSION['stock']);
							exit;
									}
									else{
										echo"
										<script>
										$().customAlert();
										alert('Sorry!', '<p>Transaction Failure.Please Repeat the transaction.</p>');
										e.preventDefault();
										</script>";
									}
							
							break;
							
							
							case 13:
							$rcptno=date('dHis').RAND(1,99);
							$max=count($_SESSION['expense']);
							for ($i = 0; $i < $max; $i++){
							$qsold=0;
							$itcode = $_SESSION['expense'][$i][0];
							$itname = $_SESSION['expense'][$i][1];
							$amount = $_SESSION['expense'][$i][2];
							$date = $_SESSION['expense'][$i][3];
							$stamp=preg_replace('~/~', '', $date);
			
$resulta = mysqli_query($db,"insert into expenses values('','".$itcode."','".$itname."','".$amount."','".$date."','".$stamp."')");	
					
				}
				if($resulta){
			unset($_SESSION['expense']);
			echo'<embed src="images/honk3.wav" autostart="true" width="0" height="0" id="sound1" enablejavascript="true">';
							echo"<script>$('#totitems').val('');$('#fintot').val('');</script>";
						
										echo"
										<script>
										$().customAlert();
										alert('Success!', '<p>Transaction Successful.</p>');
										e.preventDefault();
										</script>";
										exit;
									}
									else{
								$result = mysqli_query($db,"DELETE from expenses where EntryNo='".$rcptno."'");
										echo"
										<script>
										$().customAlert();
										alert('Sorry!', '<p>Transaction Failure.Please Repeat the transaction.</p>');
										e.preventDefault();
										</script>";
									}
							
							break;
							
							case 14:
							$tname=$_GET['tname'];
							$comme=$_GET['comme'];
							$date=date('d/m/y');
							
							$result = mysqli_query($db,"insert into chat values('','".$date."','".$tname."','".$comme."',1)");
							if($result){
								chat();	
								echo"
										<script>$('#comme').val('');</script>";
									}
							break;
							case 15:
							chat();
							break;
							
							
							
							case 19:
							$cname=$_GET['cname'];
							$web=$_GET['web'];
							$loc=$_GET['loc'];
							$motto=$_GET['motto'];
							$email=$_GET['email'];
							$tel=$_GET['tel'];
							$add=$_GET['add'];
							$vat=$_GET['vat'];
							$pin=$_GET['pin'];
							
							
							$resultc = mysqli_query($db,"update company set CompanyName='".$cname."',Tel='".$tel."',Address='".$add."',Website='".$web."',Email='".$email."',Description='".$loc."',Motto='".$motto."',VAT='".$vat."',PIN='".$pin."'");
							
							if($resultc){
		$resulta = mysqli_query($db,"insert into log values('','".$username." updates company details.','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	

										echo"<img src=\"images/tick.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
							
							break;
							
							case 20:
							$name=$_GET['name'];
							$dep=$_GET['dep'];
							$pos=$_GET['pos'];
							$pass=$_GET['pass'];
							$fname=$_GET['fname'];
							$pass=sha1($pass);
							
					$resultc = mysqli_query($db,"select * from users where name='".$name."'");
					if(mysqli_num_rows($resultc)>0){
						echo"
										<script>
										$().customAlert();
										alert('Sorry!', '<p>User name already exists.</p>');
										e.preventDefault();
										</script>";
										exit;
					}
					
							
					$result = mysqli_query($db,"insert into users values('','".$name."','".$pos."','".$pass."','".$fname."','".$dep."')");		
							if($result){
		$resulta = mysqli_query($db,"insert into log values('','".$username." inserts new User into System.User NAME:".$name."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
							
							echo"<img src=\"images/tick.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
								echo'<script>
										setTimeout(function() {
											adduser();},500);
										</script>';
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
							
							break;
							
							case 21:
							
							$user=$_GET['user'];
							$pos=$_GET['pos'];
							$dep=$_GET['dep'];
							$fname=$_GET['fname'];
							
							
					$result = mysqli_query($db,"update users set position='".$pos."',dept='".$dep."',fullname='".$fname."' where userid='".$user."'");
							if($result){
	$resulta = mysqli_query($db,"insert into log values('','".$username."  updates user data.User Id:".$user."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
							
							echo"<img src=\"images/tick.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
							echo'<script>
										setTimeout(function() {
											adduser();},500);
										</script>';
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
							
							break;
							
							case 22:
							$cash=$_GET['cash'];
							$doc=$_GET['doc'];
							$user=$_GET['user'];
							$admin=$_GET['admin'];
							$code=$_GET['code'];
							$pharm=$_GET['pharm'];
							$lab=$_GET['lab'];
							$proc=$_GET['proc'];
							$hr=$_GET['hr'];
							
							$result = mysqli_query($db,"update accesstbl set Admin='".$admin."',Manager='".$doc."',Cashier='".$cash."',User='".$user."',Accountant='".$pharm."',Owner='".$lab."',Procurement='".$proc."',HR='".$hr."' where AccessCode='".$code."'");
					
							if($result){
			$resulta = mysqli_query($db,"insert into log values('','".$username." updates user rights .User Id:".$code."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
							
							echo"<img src=\"images/tick.png\" style=\"margin-top:0px\"  width=\"22\" height=\"22\"/>";
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:0px\"  width=\"22\" height=\"22\"/>";
							break;
							
							case 23:
							$name=$_GET['name'];
							$cat=$_GET['cat'];
							$result = mysqli_query($db,"insert into ".$cat." values('','".$name."')");	
					
							if($result){
			$resulta = mysqli_query($db,"insert into log values('','".$username." inserts a new data into ".$cat." table.name:".$name."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
							
							echo"<img src=\"images/tick.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
								echo'<script>setTimeout(function() {editbranch();},500);</script>';	
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							
							break;
							case '23.1':
							$vname=$_GET['vname'];
							$sysvar=$_GET['sysvar'];
							$bid=$_GET['bid'];
							
					$result = mysqli_query($db,"update ".$sysvar." set name='".$vname."' where id='".$bid."'");		
					
						if($result){
								
	$resulta = mysqli_query($db,"insert into log values('','".$username." updates system variable.Name:".$vname."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
						
						echo'<img src="images/tick.png" style=" width:30px; height:30px; margin:0px 0 0 0px">';
								echo'<script>setTimeout(function() {editbranch();},500);</script>';	
									}
								else{
									echo'<img src="images/delete.png" style=" width:30px; height:30px; margin:-10px 0 0 0px">';
									}
							
								break;
							
							case 24:
							$sysvar=$_GET['sysvar'];
								$bid=$_GET['bid'];
								$vname=$_GET['vname'];
								$result = mysqli_query($db,"DELETE from ".$sysvar." where id='".$bid."'");
								if($result){
								
	$resulta = mysqli_query($db,"insert into log values('','".$username." deletes system variable.Name:".$vname."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
						
						echo'<img src="images/tick.png" style=" width:30px; height:30px; margin:0px 0 0 0px">';
						echo'<script>setTimeout(function() {editbranch();},500);</script>';
									
									}
								else{
									echo'<img src="images/delete.png" style=" width:30px; height:30px; margin:-10px 0 0 0px">';
									}
							
								break;

							
							$userid=$_GET['userid'];
							$name=$_GET['name'];
								
							$result = mysqli_query($db,"update users set name='".$name."' where userid=".$userid."");
					
							if($result){
$resulta = mysqli_query($db,"insert into log values('','".$username." changes username.User Id:".$userid."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
								echo'<p><img src="images/tick.png" style=" width:30px; height:30px; margin:-10px 0 0 0px"></p>';
									
									}
								else{
									echo'<p><img src="images/delete.png" style=" width:30px; height:30px; margin:-10px 0 0 0px"></p>';
									}
								break;
								
							case 26:
							
							$userid=$_GET['userid'];
							$opass=$_GET['opass'];
							$npass=$_GET['npass'];
							$cpass=$_GET['cpass'];
							$resultx =mysqli_query($db,"select * from users where userid=".$userid."");
							$row=mysqli_fetch_array($resultx);
							$kpass=stripslashes($row['password']);
							$sopass=sha1($opass);
							
							if($sopass!=$kpass){
								echo"<script>$().customAlert();
		alert('Error!', '<p>Your old password is wrong!</p>');
		e.preventDefault();</script>";
								exit;
							}
							
							if($cpass!=$npass){
									echo"<script>$().customAlert();
		alert('Error!', '<p>Your New password does not match the confirmation detail!</p>');
		e.preventDefault();</script>";
								exit;
							}
							else if($opass==$npass){
									echo"<script>$().customAlert();
		alert('Error!', '<p>Your old password cannot be the same as your new password!</p>');
		e.preventDefault();</script>";
								exit;
							}
							else if((strlen($npass) > 16) || (strlen($npass) < 6)){
									echo"<script>$().customAlert();
		alert('Error!', '<p>Password length must be between 6 and 16 characters!</p>');
		e.preventDefault();</script>";
								exit;
							}
							else {
						$pass= sha1($npass);
						$result = mysqli_query($db,"update users set password='".$pass."' where userid=".$userid."");
					
						if($result){
								
	$resulta = mysqli_query($db,"insert into log values('','".$username." changes password details.User Id:".$userid."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
						
						echo'<p><img src="images/tick.png" style=" width:30px; height:30px; margin:-10px 0 0 0px"></p>';
						echo'<script>
										setTimeout(function() {
											changelogin();},500);
										</script>';
									
									}
								else{
									echo'<p><img src="images/delete.png" style=" width:30px; height:30px; margin:-10px 0 0 0px"></p>';
									}
							}
								break;
							
							case 27:
							$name=$_GET['name'];
							$result = mysqli_query($db,"insert into expensetbl values('','".$name."')");	
							if($result){
$resulta = mysqli_query($db,"insert into log values('','".$username." inserts expense category.Name:".$name."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
							
							echo"<img src=\"images/tick.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							
							break;
							
							case 28:
							$code=$_GET['code'];
							
							$result = mysqli_query($db,"DELETE from expensetbl where ExpenseId='".$code."'");
					
							if($result){
		$resulta = mysqli_query($db,"insert into log values('','".$username." deletes expense category.Expense Id:".$code."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
							echo"<img src=\"images/tick.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							echo"<script>$('#bname2').val('');</script>";
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							
							break;
							
							case 29:
							$name=$_GET['name'];
							$nom=$_GET['nom'];
							$date=$_GET['datee'];
							$amount=$_GET['amount'];
							$stamp=preg_replace('~/~', '', $date);
							
							$result = mysqli_query($db,"insert into nominaldata values('','".$name."','".$nom."','".$date."','".$amount."','".$stamp."')");	
					
							if($result){
			$resulta = mysqli_query($db,"insert into log values('','".$username." inserts new nominal data.Name:".$nom."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
							echo"<img src=\"images/tick.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
							
							break;
							
							
							case 30:
							$stamp=date('Ymd');
							$date=date('d/m/Y');
							
							//get receipt no
							$question =mysqli_query($db,"SELECT * FROM issuetable order by TransNo desc limit 0,1");
							$ans=mysqli_fetch_array($question);
							$rcptno=stripslashes($ans['IssueNo'])+1;
							
							$max=count($_SESSION['issue']);
							for ($i = 0; $i < $max; $i++){
							$itcode = $_SESSION['issue'][$i][0];
							$itname = $_SESSION['issue'][$i][1];
							$itquat = $_SESSION['issue'][$i][2];
							$price = $_SESSION['issue'][$i][3];
							$tprice = $_SESSION['issue'][$i][4];
							$unit = $_SESSION['issue'][$i][5];
							$part = $_SESSION['issue'][$i][6];
							$tdate = $_SESSION['issue'][$i][7];
							$pack = $_SESSION['issue'][$i][8];
							$sname = $_SESSION['issue'][$i][9];
							$batch = $_SESSION['issue'][$i][10];
							$expiry = $_SESSION['issue'][$i][11];
							$tstamp=preg_replace('~/~', '', $tdate);
							
	$resulta = mysqli_query($db,"insert into issuetable values('','".$rcptno."','".$tdate."','".$itcode."','".$itname."','".$itquat."','".$unit."','".$part."','".$sname."','".$pack."','".$tstamp."','".$username."',1,'".$batch."','".$expiry."','".$price."','".$tprice."')");
	
							//update database
							$result =mysqli_query($db,"select * from items where ItemCode='".$itcode."'");
							$row=mysqli_fetch_array($result);
							$bal=stripslashes($row['Bal']);
							$nbal=$bal-$itquat;
							
							//insert into stock track
		$resultd = mysqli_query($db,"insert into stocktrack values('','".date('Y/m/d')."','".$userdep."','".$itcode."','".$itname."','".$pack."','STOCK TRANSFER TO ".$sname."','".$itquat."','".$nbal."','".$username."','".$stamp."')");	
							
							//update items
			$resultb = mysqli_query($db,"update items set Bal='".$nbal."' where ItemCode='".$itcode."'");
							

				}
					
				if($resulta){
$resulta = mysqli_query($db,"insert into log values('','".$username." makes a stock issue.Id:".$rcptno."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
			unset($_SESSION['issue']);											
			echo'<embed src="images/honk3.wav" autostart="true" width="0" height="0" id="sound1" enablejavascript="true">';
						
							echo"
										<script>
										window.open('report.php?id=8&rcptno=".$rcptno."');
										setTimeout(function() {stockissue();},500);
										$().customAlert();
										alert('Success!', '<p>Transaction Successful.</p>');
										e.preventDefault();
										</script>";
										exit;
									}
									else{
								$result = mysqli_query($db,"DELETE from issuetable where IssueNo='".$rcptno."'");
										echo"
										<script>
										$().customAlert();
										alert('Sorry!', '<p>Transaction Failure.Please Repeat the transaction.</p>');
										e.preventDefault();
										</script>";
									}
					
					
							break;
							
							
						case 31:
							$name=$_GET['name'];
							$tel=$_GET['tel'];
							$obal=$_GET['obal'];
							$result = mysqli_query($db,"insert into creditcustomers values('','".$name."','".$tel."','".$obal."')");	
						if($result){
$resulta = mysqli_query($db,"insert into log values('','".$username." adds new debtor.name:".$name."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	

							echo"<img src=\"images/tick.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							echo'<script>
										setTimeout(function() {
											creditcust();},500);
										</script>';
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							
							break;
							
							case 32:
							$name=$_GET['name'];
							$tel=$_GET['tel'];
							$cid=$_GET['cid'];
							$result = mysqli_query($db,"update creditcustomers set CustomerName='".$name."',Tel='".$tel."' where CustomerId=".$cid."");	
					
							if($result){
$resulta = mysqli_query($db,"insert into log values('','".$username." updates debtor detail.Id:".$cid."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");				echo"<img src=\"images/tick.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
							echo'<script>
										setTimeout(function() {
											creditcust();},500);
										</script>';
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
							
							break;
							
							case '32.1':
							$name=$_GET['name'];
							$amo=$_GET['amo'];
							$bal=$_GET['bal'];
							$desc=$_GET['desc'];
							$rcred=$_GET['rcred'];
							$cid=$_GET['cid'];
							$date=date('Y/m/d');
							$stamp=date('Ymd');
						$resulta = mysqli_query($db,"insert into customerdebts values('','".$cid."','".$name."','".$amo."','dr','".$bal."','','".$desc."','".$date."','".$stamp."')");	
						$resultb = mysqli_query($db,"update creditcustomers set Bal='".$bal."',RemainingCredit='".$rcred."' where CustomerId=".$cid."");	
					
							if($resulta&&$resultb){
$resulta = mysqli_query($db,"insert into log values('','".$username." posts debt to customer.Id:".$cid."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	

							echo"<img src=\"images/tick.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							echo'<script>
										setTimeout(function() {
											creditcust();},5000);
										</script>';
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							
							break;
							
							case 33:
							$name=$_GET['name'];
							$amo=$_GET['amo'];
							$rdebt=$_GET['rdebt'];
							$pmode=$_GET['pmode'];
							$desc=$_GET['desc'];
							$cid=$_GET['cid'];
							$date=date('Y/m/d');
							$stamp=preg_replace('~/~', '', $date);
							
							$resulta = mysqli_query($db,"insert into customerdebts values('','".$cid."','".$name."','".$amo."','cr','".$rdebt."','".$pmode."','".$desc."','".$date."','".$stamp."')");	
							$resultb =mysqli_query($db,"SELECT * FROM creditcustomers WHERE CustomerId='".$cid."'");
							$row=mysqli_fetch_array($resultb);
							$rcred=stripslashes($row['RemainingCredit']);
							$rcred=$rcred+$amo;
							$resultc = mysqli_query($db,"update creditcustomers set RemainingCredit='".$rcred."',Bal='".$rdebt."' where CustomerId=".$cid."");
						
							if($resulta&&$resultb&&$resultc){
$resulta = mysqli_query($db,"insert into log values('','".$username." pays customer debt.Id:".$cid."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
		echo"<img src=\"images/tick.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
							echo'<script>
										setTimeout(function() {
											creditcust();},5000);
										</script>';
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
							
							break;
							
							case 34:
							$name=$_GET['name'];
							$tel=$_GET['tel'];
							$obal=$_GET['obal'];
							$result = mysqli_query($db,"insert into creditsuppliers values('','".$name."','".$tel."','".$obal."')");	
							if($result){
$resulta = mysqli_query($db,"insert into log values('','".$username." adds new creditor.name:".$name."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	

							echo"<img src=\"images/tick.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							echo'<script>
										setTimeout(function() {
											creditsup();},500);
										</script>';
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							
							break;
							
							case 35:
							$cid=$_GET['cid'];
								
				$result = mysqli_query($db,"DELETE from creditcustomers where CustomerId='".$cid."'");
					
							if($result){
$resulta = mysqli_query($db,"insert into log values('','".$username." deletes credit customer.Id:".$cid."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
					echo"<img src=\"images/tick.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
							echo'<script>
										setTimeout(function() {
											creditcust();},500);
										</script>';
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
							
							break;
							
								case 36:
							$cid=$_GET['cid'];
							
							
				$resulta = mysqli_query($db,"DELETE from creditsuppliers where CustomerId='".$cid."'");
				
					
							if($result){
							$resulta = mysqli_query($db,"insert into log values('','".$username." deletes credit customer.Id:".$cid."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
					echo"<img src=\"images/tick.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
							echo'<script>
										setTimeout(function() {
											creditsup();},500);
										</script>';}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
							
							break;
							
						case 37:
							$name=$_GET['name'];
							$tel=$_GET['tel'];
							$cid=$_GET['cid'];
							$result = mysqli_query($db,"update creditsuppliers set CustomerName='".$name."',Tel='".$tel."' where CustomerId=".$cid."");	
					
							if($result){
$resulta = mysqli_query($db,"insert into log values('','".$username." updates creditor detail.Id:".$cid."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");				echo"<img src=\"images/tick.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
							echo'<script>
										setTimeout(function() {
											creditsup();},500);
										</script>';
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
							
							break;
							
							case '37.1':
							$name=$_GET['name'];
							$amo=$_GET['amo'];
							$bal=$_GET['bal'];
							$desc=$_GET['desc'];
							$rcred=$_GET['rcred'];
							$cid=$_GET['cid'];
							$date=date('Y/m/d');
							$stamp=date('Ymd');
						$resulta = mysqli_query($db,"insert into supplierdebts values('','".$cid."','".$name."','".$amo."','dr','".$bal."','','".$desc."','".$date."','".$stamp."')");	
						$resultb = mysqli_query($db,"update creditsuppliers set Bal='".$bal."',RemainingCredit='".$rcred."' where CustomerId=".$cid."");	
					
							if($resulta&&$resultb){
$resulta = mysqli_query($db,"insert into log values('','".$username." posts debt to supplier.Id:".$cid."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	

							echo"<img src=\"images/tick.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							echo'<script>
										setTimeout(function() {
											creditsup();},5000);
										</script>';
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:0px\"  width=\"30\" height=\"30\"/>";
							
							break;
						
						
						case 38:
							$name=$_GET['name'];
							$amo=$_GET['amo'];
							$rdebt=$_GET['rdebt'];
							$pmode=$_GET['pmode'];
							$desc=$_GET['desc'];
							$cid=$_GET['cid'];
							$date=date('Y/m/d');
							$stamp=preg_replace('~/~', '', $date);
							
						$resulta = mysqli_query($db,"insert into supplierdebts values('','".$cid."','".$name."','".$amo."','cr','".$rdebt."','".$pmode."','".$desc."','".$date."','".$stamp."')");	
						$resultc = mysqli_query($db,"update creditsuppliers set Bal='".$rdebt."' where CustomerId=".$cid."");
						
							if($resulta&&$resultc){
$resulta = mysqli_query($db,"insert into log values('','".$username." pays supplier debt.Id:".$cid."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
		echo"<img src=\"images/tick.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
							echo'<script>
										setTimeout(function() {
											creditsup();},5000);
										</script>';
							}
							else echo"<img src=\"images/delete.png\" style=\"margin-top:30px\"  width=\"30\" height=\"30\"/>";
							
							break;
							
							case 39:
	
							
							$a=$_GET['a'];
							$emp=$_GET['emp'];
							
							
							
							if(isset($_SESSION['lan'])){
								$languages=implode(";",$_SESSION['lan']);
							}else $languages=NULL;
							if(isset($_SESSION['skl'])){
								$skills=implode(";",$_SESSION['skl']);
							}else $skills=NULL;
							if(isset($_SESSION['hobby'])){
								$hobbies=implode(";",$_SESSION['hobby']);
							}else $hobbies=NULL;
							if(isset($_SESSION['exp'])){
								$experience=implode(";",$_SESSION['exp']);
							}else $experience=NULL;
							if(isset($_SESSION['edu'])){
								$education=implode(";",$_SESSION['edu']);
							}else $education=NULL;
							
						
							if($a==1){
								
							$result =mysqli_query($db,"select * from employee where emp='".$emp."'");
							$num_results = mysqli_num_rows($result);
									if($num_results>=1){
										echo"
												<script>
												$().customAlert();
												alert('Error!', '<p>Employee No. already exists in the database</p>');
												e.preventDefault();
												</script>";
										exit;
									}
							
							$resulta = mysqli_query($db,"insert into employee values('','".$_GET['emp']."','".strtoupper($_GET['fname'])."','".strtoupper($_GET['mname'])."','".strtoupper($_GET['lname'])."','".strtoupper($_GET['dob'])."','".strtoupper($_GET['mar'])."','".strtoupper($languages)."','".strtoupper($_GET['gender'])."','".strtoupper($_GET['idno'])."','".strtoupper($_GET['phone'])."','".strtoupper($_GET['phone2'])."','".strtoupper($_GET['email'])."',
							'".strtoupper($_GET['phy'])."','".strtoupper($_GET['town'])."','".strtoupper($_GET['sal'])."','".strtoupper($_GET['emptype'])."','".strtoupper($_GET['contfrom'])."','".strtoupper($_GET['contto'])."','".strtoupper($_GET['branch'])."','".strtoupper($_GET['dept'])."','".strtoupper($_GET['pos'])."','".mysqli_real_escape_string(trim($_GET['jobdesc']))."','".$_GET['bgroup']."','".mysqli_real_escape_string(trim($_GET['alergy']))."','".$_GET['ename']."','".$_GET['ephone']."','".$_GET['epostal']."','".$_GET['bid']."','".strtoupper($_GET['bname'])."','".$_GET['acno']."','".$_GET['pinno']."','".$_GET['nssf']."','".$_GET['nhif']."','".strtoupper($education)."','".strtoupper($experience)."','".strtoupper($skills)."','".strtoupper($hobbies)."','images/employees/".$emp.".jpg','0','0','".$_GET['doe']."','','','".date('Ymd')."','".date('d/m/Y')."',1,'')") or die (mysqli_error());
							}
							
							
							else{
								
							$resulta = mysqli_query($db,"update employee set fname='".strtoupper($_GET['fname'])."',mname='".strtoupper($_GET['mname'])."',lname='".strtoupper($_GET['lname'])."',dob='".strtoupper($_GET['dob'])."',marital='".strtoupper($_GET['mar'])."',languages='".strtoupper($languages)."',gender='".strtoupper($_GET['gender'])."',idno='".strtoupper($_GET['idno'])."',phone='".strtoupper($_GET['phone'])."',phone2='".strtoupper($_GET['phone2'])."',email='".strtoupper($_GET['email'])."',phyadd='".strtoupper($_GET['phy'])."',town='".strtoupper($_GET['town'])."',salary='".strtoupper($_GET['sal'])."',emptype='".strtoupper($_GET['emptype'])."',contractfrom='".strtoupper($_GET['contfrom'])."',contractto='".strtoupper($_GET['contto'])."',dept='".strtoupper($_GET['dept'])."',branch='".strtoupper($_GET['branch'])."',position='".strtoupper($_GET['pos'])."',jobdesc='".mysqli_real_escape_string(trim($_GET['jobdesc']))."',bgroup='".strtoupper($_GET['bgroup'])."',alergy='".mysqli_real_escape_string(trim($_GET['alergy']))."',ename='".strtoupper($_GET['ename'])."',ephone='".strtoupper($_GET['ephone'])."',epostal='".strtoupper($_GET['epostal'])."',bid='".strtoupper($_GET['bid'])."',bname='".strtoupper($_GET['bname'])."',acno='".strtoupper($_GET['acno'])."',pinno='".strtoupper($_GET['pinno'])."',nssf='".strtoupper($_GET['nssf'])."',nhif='".strtoupper($_GET['nhif'])."',education='".strtoupper($education)."',experience='".strtoupper($experience)."',skills='".strtoupper($skills)."',hobbies='".strtoupper($hobbies)."',employdate='".strtoupper($_GET['doe'])."' where emp='".$emp."'");
							
							
							$resultb =mysqli_query($db,"select * from payroll where status=1 and emp='".$emp."' order by serial desc limit 0,1");
							$row=mysqli_fetch_array($resultb);
							$serial=stripslashes($row['serial']);
				$resultc = mysqli_query($db,"update payroll set bid='".strtoupper($_GET['bid'])."',bname='".strtoupper($_GET['bname'])."',acno='".strtoupper($_GET['acno'])."' where serial='".$serial."'");
								
							}
							
									if($resulta){
$resulta = mysqli_query($db,"insert into log values('','".$username." inserts data into Employee database.PF No:".$emp."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
				
									echo'<img src="images/tick.png" style="margin-top:0px"  width="30" height="30"/>';
									if($a==1){
										echo'<script>setTimeout(function() {newemp();},500);</script>';
                                        }else{echo'<script>setTimeout(function() {seeemp(61);},500);</script>';}
										exit;
									}
									else{
										echo'<img src="images/delete.png" style="margin-top:0px"  width="30" height="30"/>';
										
									}
							
							break;
								
								case 43:
							$sal=$_GET['sal'];
							$name=$_GET['name'];
							$emp=$_GET['emp'];
							$mon=$_GET['mon'];
							$page=$_GET['page'];
							$allow=$_GET['allow'];
							$adva=$_GET['adva'];
							$ins=$_GET['ins'];
							$ded=$_GET['ded'];
							$othrs=$_GET['othrs'];
							$rateot=$_GET['rateot'];
							$date= date('m_Y'); 
							$dept='Others'; 
							$totalot=$rateot*$othrs;
							$net=$sal+$allow+$totalot-$adva-$ins-$ded;
							if($emp!=''){
							$result =mysqli_query($db,"select * from ".$mon." where emp='".$emp."'");
							$num_results = mysqli_num_rows($result);
							if($num_results>=1){
								echo'exists';
								exit;
							}
							
		$resulta = mysqli_query($db,"insert into ".$mon." values('','".$mon."','".$emp."','".$name."','".$dept."','".$sal."',
							'".$allow."','".$adva."','".$ins."','".$ded."','".$othrs."','".$rateot."','".$totalot."','".$net."',1)");
		$resultb = mysqli_query($db,"insert into salary values('','".$mon."','".$emp."','".$name."','".$dept."','".$sal."',
							'".$allow."','".$adva."','".$ins."','".$ded."','".$othrs."','".$rateot."','".$totalot."','".$net."','')");
		
		
		
							if($resulta&&$resultb){
									echo'<img src="images/tick.png"  width="20" height="20"/>';
									echo"<script>
										setTimeout(function() {
											pagerefresh(1,9,'".$mon."');},1000);
										</script>
									";
									
									}
								else{
									echo'<img src="images/delete.png"  width="20" height="20"/>';
									}
									
							}
							else{
									echo'<img src="images/delete.png"  width="20" height="20"/>';
									}
								break;
								
							case 44:
							$emp=$_GET['emp'];
							$page=$_GET['page'];
							$mon=$_GET['mon'];
						
							echo'<p style="display:none" id="empno">'.$emp.'</p>';
							$resulta = mysqli_query($db,"DELETE from salary where emp='".$emp."'");
							$resultb = mysqli_query($db,"DELETE from ".$mon." where emp='".$emp."'");
							if($resulta&&$resultb){
									echo'
									<img src="images/delete.png"  width="20" height="20"/>';
									echo"<script>
										setTimeout(function() {
											pagerefresh(".$page.",9,'".$mon."');},1000);
										</script>
									";
									}
								
								break;
								
								
							case 45:
							$mon=$_GET['mon'];
							$totalnet=0;$totalsal=0;$totalallow=0;$totaladva=0;$totalins=0;$totalded=0;$totalothrs=0;$totalotal=0;
							$result =mysqli_query($db,"select * from ".$mon."");
							$num_results = mysqli_num_rows($result);
							for ($i=0; $i <$num_results; $i++) {
							$row=mysqli_fetch_array($result);
							$net=stripslashes($row['net']);
							$sal=stripslashes($row['sal']);
							$allow=stripslashes($row['allow']);
							$adva=stripslashes($row['adva']);
							$ins=stripslashes($row['ins']);
							$ded=stripslashes($row['ded']);
							$othrs=stripslashes($row['othrs']);
							$totalot=stripslashes($row['totalot']);
							$totalnet+=$net;
							$totalsal+=$sal;
							$totalallow+=$allow;
							$totaladva+=$adva;
							$totalins+=$ins;
							$totalded+=$ded;
							$totalothrs+=$othrs;
							$totalotal+=$totalot;}
							
							
							$result =mysqli_query($db,"select * from salaryledger where month='".$mon."'");
							$num_results = mysqli_num_rows($result);
							for ($i=0; $i <$num_results; $i++) {
							$row=mysqli_fetch_array($result);}
							$balance=stripslashes($row['Balance']);
							$stamp=stripslashes($row['stamp']);
							$am=stripslashes($row['amount']);
							
		$resultb = mysqli_query($db,"update salaryledger set amount='".$totalnet."',sal='".$totalsal."',allow='".$totalallow."',adva='".$totaladva."',ins='".$totalins."',ded='".$totalded."',othrs='".$totalothrs."',totalot='".$totalotal."',status=0 where month='".$mon."'");
							$totalnet-=$am;
							
							
							$result =mysqli_query($db,"select * from salaryledger where stamp>=".$stamp."");
							$num_results = mysqli_num_rows($result);
							for ($i=0; $i <$num_results; $i++) {
							$row=mysqli_fetch_array($result);
							$monn=stripslashes($row['month']);
							$balance=stripslashes($row['Balance']);
							$balance-=$totalnet;
							$resultc = mysqli_query($db,"update salaryledger set Balance='".$balance."' where month='".$monn."'");
							$balance=0;
							}
							
							if($resultc){
									echo'<img src="images/tick.png"  width="30" height="30"/>';
									
									}
								else{
									echo'<img src="images/delete.png"  width="30" height="30"/>';
									}
								
									echo"<script>
										setTimeout(function() {
											location.reload(true);},1000);
										</script>
									";
								break;
								
								
						case 46:
							$regn=$_GET['regn'];
							$tbl=$_GET['tbl'];
							
						$result = mysqli_query($db,"update ".$tbl." set status=1 where regn='".$regn."'");
							
							if($result){
									echo'<img src="images/tick.png" width="20px" height="20px"/>';
																		
									}
								else{
									echo'<img src="images/delete.png" width="20px" height="20px"/>';
									}
								
									
						break;
						
						case 47:
						$a=$_GET['a'];
						echo $a;
						$result =mysqli_query($db,"select * from items where ItemCode=".$a."");
							$row=mysqli_fetch_array($result);
								$vat=stripslashes($row['Vat']);
								$itemp=stripslashes($row['SalePrice']);
								$item=stripslashes($row['ItemName']);
								$code=stripslashes($row['ItemCode']);
								$itcost = stripslashes($row['PurchPrice']);
								$bal = stripslashes($row['Bal']);
								$qsold = stripslashes($row['Qsold']);
								
								echo"<script>
								$('#total').val('');
								$('#disc').val('');
								$('#price').val(".$itemp.");
								$('#red').val('".$item."');
								$('#code').val(".$code.");
								$('#itcost').val(".$itcost.");
								$('#vat').val(".$vat.");
								$('#bal').val(".$bal.");
								$('#qsold').val(".$qsold.");
								if($('#quat').val()!=''){
								var quat = $('#quat').val();
								var tot=".$itemp." * quat;
								sot=(tot).formatMoney(2, '.', ',');
								$('#tprice').val(sot);
								vat=".$vat." * quat;
								tot=parseInt(tot,10) + parseInt(vat,10);
								tot=(tot).formatMoney(2, '.', ',');
								$('#total').val(tot);
								vat=(vat).formatMoney(2, '.', ',');
								$('#tvat').val(vat);
								}
								$('#quat').focus();
									</script>";
								
								echo $item;
								break;
							
							
							
							case 56:
							$eid=$_GET['a'];
							$result= mysqli_query($db,"update mytasks set Status=0 where Event_id=".$eid."");
							break;
							case '56.1':
							$eid=$_GET['a'];
							$result= mysqli_query($db,"update messages set status=1 where id=".$eid."");
							break;
							
							case '56.2':
							$b=substr($_GET['to'],0,1);
							
							$len=strlen($_GET['to']);
							$len-=1;
							$c=substr($_GET['to'],1,$len);
							
							if($b==1){
							$result = mysqli_query($db,"insert into messages values('','".$c."','".$_GET['a']."','".$_GET['mess']."','".date('d/m/Y-H:i')."','".date('Ymd')."',0)");}
							if($b==2){
							$resulta =mysqli_query($db,"select * from users where dept='".$c."'");
							$num_resultsa = mysqli_num_rows($resulta);	
								for ($i=0; $i <$num_resultsa; $i++) {
									$rowa=mysqli_fetch_array($resulta);  
							$result = mysqli_query($db,"insert into messages values('','".stripslashes($rowa['name'])."','".$_GET['a']."','".$_GET['mess']."','".date('d/m/Y-H:i')."','".date('Ymd')."',0)");	
								}
							}
							if($b==3){
							$resulta =mysqli_query($db,"select * from users");
							$num_resultsa = mysqli_num_rows($resulta);	
								for ($i=0; $i <$num_resultsa; $i++) {
									$rowa=mysqli_fetch_array($resulta);  
							$result = mysqli_query($db,"insert into messages values('','".stripslashes($rowa['name'])."','".$_GET['a']."','".$_GET['mess']."','".date('d/m/Y-H:i')."','".date('Ymd')."',0)");	
								}
							}
							if($result){
								echo"<script>
										$('#newmessage').dialog( 'close' );
										refreshmytasks();
										</script>"	;
							}else{echo"
										<script>
										$().customAlert();
										alert('Failure!', '<p>Message not sent.</p>');
										e.preventDefault();
										</script>";}
							break;
							case '56.3':
							$a=$_GET['a'];
							$result= mysqli_query($db,"update messages set status=1 where name='".$a."'");
							break;
							
							case 57:
							$task=$_GET['task'];
							$uid=$_GET['uid'];
							$date=date('d/m/Y');
							$time=date('H:i');
							$result = mysqli_query($db,"insert into mytasks values('','".$task."','','','','','','".$date."','".$time."','','','','','',1,'".$uid."','','')");
							if($result){
						echo"<script>$('#taskfield').val('');
						refreshmytasks();</script>";
									}
							break;
							
							case 58:
							
							$subj=$_GET['subj'];
							$cate=$_GET['cate'];
							$crex=$_GET['crex'];
							$eid=$_GET['eid'];
							$loc=$_GET['loc'];
							$due=$_GET['due'];
							$duet=$_GET['duet'];
							$star=$_GET['star'];
							$start=$_GET['start'];
							$remind=$_GET['remind'];
							$rem=$_GET['rem'];
							$remt=$_GET['remt'];
							$status=$_GET['status'];
							$pri=$_GET['pri'];
							$comp=$_GET['comp'];
							$notes=$_GET['notes'];
							$a=preg_replace('~/~', '', $rem);
							$b=substr($remt,0,2);
							$c=substr($remt,3,2);
							$d=substr($remt,5,2);
							if($d=='PM'){
								$b=sprintf("%02d",$b+12);
							}
							$stamp=$a.$b.$c;
							
							if($crex==1){
							$result = mysqli_query($db,"insert into mytasks values('','".$subj."','".$cate."','".$loc."','".$status."','".$pri."','".$comp."','".$star."','".$start."','".$due."','".$duet."','".$remind."','".$rem."','".$remt."',1,'".$userid."','".$notes."','".$stamp."')");
							}
							if($crex==2){
							$result = mysqli_query($db,"update mytasks set Reason='".$subj."',Category='".$cate."',Location='".$loc."',TaskStatus='".$status."',Priority='".$pri."',Complete='".$comp."',StartDate='".$star."',StartTime='".$start."',DueDate='".$due."',DueTime='".$duet."',ReminderStatus='".$remind."',ReminderDate='".$rem."',ReminderTime='".$remt."',User_id='".$userid."',Notes='".$notes."',Stamp='".$stamp."' where Event_id='".$eid."'");	
							}
							if($result){
									echo'<p><img src="images/tick.png" style="margin-top:0px; margin-right:10px; width:20px; height:20px"></p>
									<script>refreshmytasks()</script>';
									echo"<script>$('#crex').val(1);$('#subject').val('');$('#location').val('');$('#category').val('');$('#complete').val('');
									$('#priority').val('');$('#status').val('');</script>";
									exit;
									}
									else{
										echo'<p><img src="images/delete.png" style="margin-top:0px; width:20px; height:20px"></p>';
									}
							break;
							
							case 59:
							$sno=$_GET['sno'];
							$eid=$_GET['eid'];
							$stamp=date('YmdHi');
							$a=substr($stamp,10,2);
							$f=$stamp+$sno;
							$b=$a+$sno;
							if($b>59){
								$c=substr($stamp,8,2);
								$c++;
								$z=$b-60;
								$b=sprintf("%02d",$z);
								$f=substr($stamp,0,8).$c.$b;
								if($c>23){
								$d=substr($stamp,6,2);
								$d++;
								$y=24-$c;
								$c=sprintf("%02d",$y);
								$f=substr($stamp,0,6).$d.$c.$b;
								}
							}
							$g=substr($f,0,4);
							$h=substr($f,4,2);
							$i=substr($f,6,2);
							$j=substr($f,8,2);
							$k=substr($f,10,2);
							$l=$g.'/'.$h.'/'.$i;//date
							if($j>12){
								$s='PM';
								$j=$j-12;
								$j=sprintf("%02d",$j);
							}else $s='AM';
							$m=$j.':'.$k.$s;//time
							if($sno!=0){
							$result= mysqli_query($db,"update mytasks set ReminderStatus=1,Stamp='".$f."',ReminderDate='".$l."',ReminderTime='".$m."' where Event_id=".$eid."");
							if($result){
							echo'<p><img src="images/tick.png" style="margin-top:0px; margin-right:10px; width:20px; height:20px"></p>';
							}
							}
							break;
						
							case 60:
							taska();
							break;
							
							case '60.1':
	//check for system controls:
	//1.Accountant cash in hand limit:
	$result =mysqli_query($db,"select * from ledgers where ledgerid='626'");
	$row=mysqli_fetch_array($result);
	$bal=stripslashes($row['bal']);
	if($bal>500000){
	$resultc =mysqli_query($db,"select * from messages where message='Accountant Cash in Hand Limit exceeded-".date('d/m/Y')."' order by id desc limit 0,1000");	
	$num_resultsc = mysqli_num_rows($resultc);	
	if($num_resultsc==0){	
		$resulta =mysqli_query($db,"select * from users where position='Admin' order by name");
							$num_resultsa = mysqli_num_rows($resulta);	
							for ($i=0; $i <$num_resultsa; $i++) {
								$rowa=mysqli_fetch_array($resulta);  
								$name=stripslashes($rowa['name']);
								$resultb = mysqli_query($db,"insert into messages values('','".$name."','System','Accountant Cash in Hand Limit exceeded-".date('d/m/Y')."','".date('d/m/Y-H:i')."','".date('Ymd')."',0)");
	
							}
		}
	}
	
	//2.Discount authorized above certain limit.
	$result =mysqli_query($db,"select * from sales where Discount>10000 and Stamp='".date('Ymd')."' and Status=1");
	$num_results = mysqli_num_rows($result);	
	for ($i=0; $i <$num_results; $i++) {
	$row=mysqli_fetch_array($result);  
	$resultc =mysqli_query($db,"select * from messages where message='Discount of ".stripslashes($row['Discount'])." posted by ".stripslashes($row['Posted'])."' order by id desc limit 0,1000");	
	$num_resultsc = mysqli_num_rows($resultc);	
	if($num_resultsc==0){	
		$resulta =mysqli_query($db,"select * from users where position='Admin' or position='Accountant' order by name");
							$num_resultsa = mysqli_num_rows($resulta);	
							for ($i=0; $i <$num_resultsa; $i++) {
								$rowa=mysqli_fetch_array($resulta);  
								$name=stripslashes($rowa['name']);
								$resultb = mysqli_query($db,"insert into messages values('','".$name."','System','Discount of ".stripslashes($row['Discount'])." posted by ".stripslashes($row['Posted'])."','".date('d/m/Y-H:i')."','".date('Ymd')."',0)");
	
							}				
	}
	}
	
	
	//5.Drugs fall below a certain limit
	$result =mysqli_query($db,"select * from items where MinBal>Bal and Type='GOOD'");
			$num_results = mysqli_num_rows($result);	
			for ($i=0; $i <$num_results; $i++) {
			$row=mysqli_fetch_array($result);  
				$resultc =mysqli_query($db,"select * from messages where message='The item ".stripslashes($row['ItemName'])." is below the minimum stock balance. It is advised you stock the item.' order by id desc limit 0,1000");	
	$num_resultsc = mysqli_num_rows($resultc);	
	if($num_resultsc==0){	
		$resulta =mysqli_query($db,"select * from users where position='Procurement' or position='Admin' order by name");
							$num_resultsa = mysqli_num_rows($resulta);	
							for ($i=0; $i <$num_resultsa; $i++) {
								$rowa=mysqli_fetch_array($resulta);  
								$name=stripslashes($rowa['name']);
								$resultb = mysqli_query($db,"insert into messages values('','".$name."','System','The item ".stripslashes($row['ItemName'])." is below the minimum stock balance. It is advised you stock the item.','".date('d/m/Y-H:i')."','".date('Ymd')."',0)");
	
							}				
	}	
}
	
		
	//update ledgers
	
	$result = mysqli_query($db,"select * from ledgers where date='".date('d/m/Y')."'");
	$num_results = mysqli_num_rows($result);
	if($num_results==0){
		$date=date('Y/m/d');
						$stamp=date('Ymd');
						$result = mysqli_query($db,"select * from ledgers WHERE ledgerid!=601 order by name");
						$num_results = mysqli_num_rows($result);
						for ($i=0; $i <$num_results; $i++) {
						$row=mysqli_fetch_array($result);
						$lid=stripslashes($row['ledgerid']);
						$name=stripslashes($row['name']);
						$bal=stripslashes($row['bal']);
						$resultc = mysqli_query($db,"insert into generalledger values('','".$lid."','".$name."','".$date."','".$bal."','".$stamp."',1)");
						}
			$resultf = mysqli_query($db,"update ledgerentries set status=0 where status!=0");
			$resultg = mysqli_query($db,"update ledgers set date='".date('d/m/Y')."'");
			$resulta = mysqli_query($db,"insert into log values('','".$username." posts entries to general ledger.','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
	
	}	
	
	//back up database
	$result = mysqli_query($db,"select * from backup where date='".date('d/m/Y')."'");
	$num_results = mysqli_num_rows($result);
	if($num_results==0){
	//echo"<script>window.location.href = 'http://code.dev/qubiz/database_backup/backup.php';</script>";
	}	
	
						
							
							break;
							
							case 61:
							
							$subj=$_GET['subj'];
							$cate=$_GET['cate'];
							$crex=$_GET['crex'];
							$eid=$_GET['eid'];
							$loc=$_GET['loc'];
							$end=$_GET['end'];
							$endt=$_GET['endt'];
							$star=$_GET['star'];
							$datestamp=preg_replace('~/~', '', $_GET['star']);
							$start=$_GET['start'];
							$remind=$_GET['remind'];
							$rem=$_GET['rem'];
							$remt=$_GET['remt'];
							$status=$_GET['status'];
							$pri=$_GET['pri'];
							$comp=$_GET['comp'];
							$notes=$_GET['notes'];
							$pid=$_GET['pid'];
							$rece=preg_replace('~/~', '', $_GET['rece']);
							$recs=$_GET['recs'];
							$rpat=$_GET['rpat'];
							$reccur=$_GET['reccur'];
							$pat=$_GET['pat'];
							
							$a=preg_replace('~/~', '', $rem);
							$b=substr($remt,0,2);
							$c=substr($remt,3,2);
							$d=substr($remt,5,2);
							
							if($d=='PM'){
								$b=sprintf("%02d",$b+12);
							}
							$stamp=$a.$b.$c;
							
							//update database
							
							if($crex==1){
							$result = mysqli_query($db,"insert into calendar values('','".$pid."','".$pat."','".$subj."','".$cate."','".$loc."','','".$pri."','".$comp."','".$status."','".$star."','".$start."','".$end."','".$endt."','".$remind."','".$rem."','".$remt."',1,'".$reccur."','".$rpat."','".$recs."','".$rece."','".$userid."','".$notes."','".$stamp."','".$datestamp."')");
							}
							if($crex==2){
							$result = mysqli_query($db,"update calendar set Cus_name='".$pat."',Cus_id='".$pid."',Reason='".$subj."',Category='".$cate."',Location='".$loc."',TaskStatus='".$status."',Priority='".$pri."',Complete='".$comp."',StartDate='".$star."',StartTime='".$start."',EndDate='".$end."',EndTime='".$endt."',ReminderStatus='".$remind."',ReminderDate='".$rem."',ReminderTime='".$remt."',User_id='".$userid."',Notes='".$notes."',Stamp='".$stamp."',DateStamp='".$datestamp."',ReccurenceStatus='".$reccur."',ReccurPattern='".$rpat."',ReccurStart='".$recs."',ReccurEnd='".$rece."' where Event_id='".$eid."'");	
							
							}
							if($result){
									echo'<p><img src="images/tick.png" style="margin-top:0px; margin-right:10px; width:20px; height:20px"></p>
									<script>refreshtoday();</script>';
									echo"<script>$('#crex2').val(1);$('#subject2').val('');$('#location2').val('');$('#category'2).val('');$('#complete2').val('');
									$('#priority2').val('');$('#status2').val('');$('#patname2').val('');</script>";
									exit;
									}
									else{
										echo'<p><img src="images/delete.png" style="margin-top:0px; width:20px; height:20px"></p>';
									}
							break;
							
							case 62:
							$sno=$_GET['sno'];
							$eid=$_GET['eid'];
							$stamp=date('YmdHi');
							$a=substr($stamp,10,2);
							$f=$stamp+$sno;
							$b=$a+$sno;
							if($b>59){
								$c=substr($stamp,8,2);
								$c++;
								$z=$b-60;
								$b=sprintf("%02d",$z);
								$f=substr($stamp,0,8).$c.$b;
								if($c>23){
								$d=substr($stamp,6,2);
								$d++;
								$y=24-$c;
								$c=sprintf("%02d",$y);
								$f=substr($stamp,0,6).$d.$c.$b;
								}
							}
							$g=substr($f,0,4);
							$h=substr($f,4,2);
							$i=substr($f,6,2);
							$j=substr($f,8,2);
							$k=substr($f,10,2);
							$l=$g.'/'.$h.'/'.$i;//date
							if($j>12){
								$s='PM';
								$j=$j-12;
								$j=sprintf("%02d",$j);
							}else $s='AM';
							$m=$j.':'.$k.$s;//time
							if($sno!=0){
							$result= mysqli_query($db,"update calendar set ReminderStatus=1,Stamp='".$f."',ReminderDate='".$l."',ReminderTime='".$m."' where Event_id=".$eid."");
							if($result){
							echo'<p><img src="images/tick.png" style="margin-top:0px; margin-right:10px; width:20px; height:20px"></p>';
							}
							}
							break;
							
							
							
							case 64:
							$cusno=$_GET['param'];
							$result = mysqli_query($db,"DELETE from customers where cusno='".$cusno."'");
							if($result){
						$resulta = mysqli_query($db,"insert into log values('','".$username." deletes data from customers database.Customer No:".$cusno."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
								echo"<script>
										$().customAlert();
										alert('Success!', '<p>Delete Succesful.</p>');
										e.preventDefault();
										</script>"	;
							}else{echo"
										<script>
										$().customAlert();
										alert('Sorry!', '<p>Delete not Succesful.</p>');
										e.preventDefault();
										</script>";}
							break;
							
							
							
							case 69:
							$code=$_GET['user'];
							$result = mysqli_query($db,"DELETE from users where userid=".$code."");
							$resulta = mysqli_query($db,"insert into log values('','".$username." deletes User from System.User Id:".$code."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
							if($result){
									echo'<p><img src="images/tick.png" style="width:30px; height:30px; margin-top:20px"></p>';
								echo'<script>
										setTimeout(function() {
											adduser();},500);
										</script>';
									}
									else{
										echo'<p><img src="images/delete.png" style="width:30px; height:30px;margin-top:20px"></p>';
										}
							break;
							
							
								case '23.2':
								
							$cr=$_GET['cr'];
							$dr=$_GET['dr'];
							$drname=$_GET['drname'];
							$crname=$_GET['crname'];
							$desc=$_GET['desc'];
							$amount=$_GET['amount'];
							$date=date('Y/m/d');
							$stamp=date('Ymd');
							
						
						//this is a debit	
						$resulta = mysqli_query($db,"select * from ledgers where ledgerid=".$dr."");
						$row=mysqli_fetch_array($resulta);
						$bald=stripslashes($row['bal']);
						$type=stripslashes($row['type']);
						if($type=='Liability'||$type=='Revenue'||$type=='Equity'||$type=='Drawings'){
						$bald=$bald-$amount;
						if($bald<0){
								echo"<script>
									$().customAlert();
									alert('Error!', '<p>Liability/Revenue/Equity Subledger balance cannot be less than zero.</p>');
									e.preventDefault();
									</script>";
									exit;
						}
						
						}
						else{
						$bald=$bald+$amount;
						}
						//this is a credit
						$resulta = mysqli_query($db,"select * from ledgers where ledgerid=".$cr."");
						$row=mysqli_fetch_array($resulta);
						$balc=stripslashes($row['bal']);
						$type=stripslashes($row['type']);
						if($type=='Asset'||$type=='Expense'){
						$balc=$balc-$amount;
						if($balc<0){
								echo"<script>
									$().customAlert();
									alert('Error!', '<p>Asset/Expense Subledger balance cannot be less than zero.</p>');
									e.preventDefault();
									</script>";
									exit;
						}
						
						}
						else{
						$balc=$balc+$amount;
						}
						
						
			$resultb = mysqli_query($db,"insert into ledgerentries values('','".$cr."','".$crname."','".$dr."','".$drname."','".$amount."','".$desc."','".$date."','".$balc."','".$bald."','".$stamp."',1)");
			$resulte = mysqli_query($db,"update ledgers set bal='".$balc."' where ledgerid='".$cr."'");
			$resultf = mysqli_query($db,"update ledgers set bal='".$bald."' where ledgerid='".$dr."'");
			
			if($resultb&&$resulte&&$resultf){
			$resulta = mysqli_query($db,"insert into log values('','".$username." makes a journal entry','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
								echo'<img src="images/tick.png" style=" width:30px; height:30px; margin:0px 0 0 0px">';
								echo"<script>setTimeout(function() {journalent();},500);</script>";
									}
								else{
									echo'<img src="images/delete.png" style=" width:30px; height:30px; margin:0px 0 0 0px">';
									}
								break;
								
								case '24.2':
								
							$code=$_GET['code'];
							$date=date('Y/m/d');
							$stamp=date('Ymd');
							
						$resulta = mysqli_query($db,"select * from ledgerentries where transid=".$code."");
						$row=mysqli_fetch_array($resulta);
						$cr=stripslashes($row['crid']);
						$dr=stripslashes($row['drid']);
						$crname=stripslashes($row['crname']);
						$drname=stripslashes($row['drname']);
						$amount=stripslashes($row['amount']);
						
						//this is a debit	
						$resulta = mysqli_query($db,"select * from ledgers where ledgerid=".$cr."");
						$row=mysqli_fetch_array($resulta);
						$balc=stripslashes($row['bal']);
						$type=stripslashes($row['type']);
						if($type=='Liability'||$type=='Revenue'||$type=='Equity'||$type=='Drawings'){
						$balc=$balc-$amount;
						if($balc<0){
								echo"<script>
									$().customAlert();
									alert('Error!', '<p>Liability/Revenue/Equity Subledger balance cannot be less than zero.</p>');
									e.preventDefault();
									</script>";
									exit;
						}
						
						}
						else{
						$balc=$balc+$amount;
						}
						$desc='reversal';
						//this is a credit
						$resulta = mysqli_query($db,"select * from ledgers where ledgerid=".$dr."");
						$row=mysqli_fetch_array($resulta);
						$bald=stripslashes($row['bal']);
						$type=stripslashes($row['type']);
						if($type=='Asset'||$type=='Expense'){
						$bald=$bald-$amount;
						if($bald<0){
								echo"<script>
									$().customAlert();
									alert('Error!', '<p>Asset/Expense Subledger balance cannot be less than zero.</p>');
									e.preventDefault();
									</script>";
									exit;
						}
						
						}
						else{
						$bald=$bald+$amount;
						}
						
						
						$resulta = mysqli_query($db,"select * from ledgerentries order by transid desc limit 0,1");
						$row=mysqli_fetch_array($resulta);
						$codea=stripslashes($row['transid'])+1;
						
			$resultb = mysqli_query($db,"insert into ledgerentries values('".$codea."','".$dr."','".$drname."','".$cr."','".$crname."','".$amount."','".$desc."','".$date."','".$bald."','".$balc."','".$stamp."',3)");
			$resultg = mysqli_query($db,"update ledgerentries set status=3 where transid='".$code."'");
			$resulte = mysqli_query($db,"update ledgers set bal='".$balc."' where ledgerid='".$cr."'");
			$resultf = mysqli_query($db,"update ledgers set bal='".$bald."' where ledgerid='".$dr."'");
			
						if($resultb&&$resulte&&$resultf&&$resultg){
			$resulta = mysqli_query($db,"insert into log values('','".$username." makes a journal entry reversal.Id:".$code."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
								echo'<img src="images/tick.png" width="22px" height="22px"/>';
								}
								else{
									echo'<img src="images/tick.png" width="22px" height="22px"/>';
									}
								break;
								
								case '25.2':
								
						$date=date('Y/m/d');
						$stamp=date('Ymd');
						$result = mysqli_query($db,"select * from ledgers WHERE ledgerid!=601 order by name");
						$num_results = mysqli_num_rows($result);
						for ($i=0; $i <$num_results; $i++) {
						$row=mysqli_fetch_array($result);
						$lid=stripslashes($row['ledgerid']);
						$name=stripslashes($row['name']);
						$bal=stripslashes($row['bal']);
						$resultc = mysqli_query($db,"insert into generalledger values('','".$lid."','".$name."','".$date."','".$bal."','".$stamp."',1)");
						}
			$resultf = mysqli_query($db,"update ledgerentries set status=0 where status!=0");
			$resultg = mysqli_query($db,"update ledgers set date='".date('d/m/Y')."'");
			$resulta = mysqli_query($db,"insert into log values('','".$username." posts entries to general ledger.','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
			echo'<img src="images/tick.png" width="30px" height="30px"/>';
									
								
								break;
							
							case '29.2':
					$resultb = mysqli_query($db,"select * from ledgers order by ledgerid desc");
					$rowb=mysqli_fetch_array($resultb);
					$lid=stripslashes($rowb['ledgerid'])+1;
							$result= mysqli_query($db,"insert into ledgers values('".$lid."','".$_GET['ledger']."','".$_GET['type']."',0,1,'')");
							$resulta = mysqli_query($db,"insert into log values('','".$username." inserts data into ledgers database.Ledger name:".$_GET['ledger']."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
					
							if($result){
									echo'<p><img src="images/tick.png" style="width:30px; height:30px"></p>';
									echo "<script>paginate(8,0);</script>";
									}
									else{
										echo'<p><img src="images/delete.png" style="width:30px; height:30px"></p>';
										}
							
							break;	
							case '30.2':
							$code=$_GET['code'];
							$result = mysqli_query($db,"DELETE from ledgers where ledgerid=".$code."");
							$resulta = mysqli_query($db,"insert into log values('','".$username." deletes data from ledger database.Legder Id:".$code."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	

							break;
							case '31.2':
							$code=$_GET['code'];
							$type=$_GET['type'];
							$bal=$_GET['bal'];
							$result = mysqli_query($db,"update ledgers set type='".$_GET['type']."',bal='".$_GET['bal']."',status=1 where ledgerid='".$code."'");	
							$resulta = mysqli_query($db,"insert into log values('','".$username." updates ledgers database.Ledger Id:".$code."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
							if($result){
									echo'<p><img src="images/tick.png" style="width:16px; height:16px; margin-top:-10px; margin-left:20px"></p>';
									}
									else{
										echo'<p><img src="images/delete.png" style="width:16px; height:16px; margin-top:0px"></p>';
									}
							break;
							
							case '32.2':
							$mon=$_GET['mon'];
									$query =mysqli_query($db,"select * from salregister where month='".$mon."'");
									$count = mysqli_num_rows($query);
									if($count>0){
									echo"<script>
									$().customAlert();
									alert('Error!', '<p>Month has already been registered.</p>');
									e.preventDefault();
									</script>";	
										
									}
									else{
								$resultb =mysqli_query($db,"select * from salary where status=1");
								$num_results = mysqli_num_rows($resultb);
								for ($i=0; $i <$num_results; $i++) {
								$row=mysqli_fetch_array($resultb);
	$result = mysqli_query($db,"insert into payroll values('','".$mon."','".stripslashes($row['emp'])."','".stripslashes($row['name'])."','".stripslashes($row['dept'])."','".stripslashes($row['sal'])."','".stripslashes($row['allow'])."','".stripslashes($row['adva'])."','".stripslashes($row['ins'])."','".stripslashes($row['ded'])."','".stripslashes($row['tax'])."','".stripslashes($row['nhif'])."','".stripslashes($row['nssf'])."','".stripslashes($row['othrs'])."','".stripslashes($row['rateot'])."','".stripslashes($row['totalot'])."','".stripslashes($row['net'])."',0)");
									}
								if($result){
	$resulta = mysqli_query($db,"insert into log values('','".$username." adds new payroll.Month:".$mon."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
	$result= mysqli_query($db,"insert into salregister values('".$mon."','','','','','','','','','','','','','',1)");						
									echo'<p><img src="images/tick.png" style="width:30px; height:30px; margin-top:-5px"></p>';
									echo "<script>paginate(11,'".$mon."');</script>";
									}
									else{
										echo'<p><img src="images/delete.png" style="width:30px; height:30px;margin-top:-5px"></p>';
										}
										
									}
							break;
							
							case '33.2':
							$mon=$_GET['mon'];
									$query =mysqli_query($db,"select * from salregister where month='".$mon."' and status=1");
									$count = mysqli_num_rows($query);
									if($count==0){
									echo"<script>
									$().customAlert();
									alert('Error!', '<p>Payroll for Month does not exist or has already been posted.</p>');
									e.preventDefault();
									</script>";	
										
									}
									else{
	$resulta = mysqli_query($db,"insert into log values('','".$username." edits payroll.Month:".$mon."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
									echo "<script>payroll('".$mon."');</script>";
										
									}
							break;
							case '34.2':
							$ser=$_GET['ser'];
							$result = mysqli_query($db,"DELETE from payroll where serial='".$ser."'");
							$resulta = mysqli_query($db,"insert into log values('','".$username." deletes data from payroll','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	

							break;
							
							case '35.2':
							$sal=$_GET['sal'];if($sal==''){$sal=0;}
							$emp=$_GET['emp'];
							$mon=$_GET['mon'];
							$rateot=$_GET['rate'];
							$days=$_GET['days'];
							$allow=$_GET['allow'];if($allow==''){$allow=0;}
							$adva=$_GET['adva'];if($adva==''){$adva=0;}
							$ins=$_GET['ins'];if($ins==''){$ins=0;}
							$ded=$_GET['ded'];if($ded==''){$ded=0;}
							$othrs=$_GET['othrs'];if($othrs==''){$othrs=0;}
							$date= date('m_Y');  
							
							
							//calculate  overtime
							$totalot=$othrs*$rateot;
							
							//calculate gross income
							
							//calculate attendance deductions
							//if($days<26){
							//$sal=$sal*$days/26;
							//$sal=round($sal,2);
							//}
							
							$gross=$sal+$allow+$totalot;
							
							
							
							//calculate nssf
							$resulta =mysqli_query($db,"select * from nssf where id=1");
							$rowa=mysqli_fetch_array($resulta);
							$pnssf=stripslashes($rowa['amount']);
							
							if($gross<6000){$nssf=0;}
							else if($gross>=6000&&$gross<=18000){
								$nssf=$pnssf*$gross/100;
							}
							else if($gross>18000){$nssf=1080;}
							else{$nssf=0;}
							
							//calculate nhif
							$resulta =mysqli_query($db,"select * from nhif where ".$gross.">=lower and ".$gross."<=upper");
							$rowa=mysqli_fetch_array($resulta);
							$nhif=stripslashes($rowa['amount']);
							
							
							
							//deduct nssf
							$net=$taxnet=$gross-$nssf;
							
							
						
							//calculate tax
							
							
							$resultx =mysqli_query($db,"select * from tax where id=1");
							$rowx=mysqli_fetch_array($resultx);
							$u1=stripslashes($rowx['upper']);//10164
							$t1=stripslashes($rowx['tax']);//0
							
							$resultx =mysqli_query($db,"select * from tax where id=2");
							$rowx=mysqli_fetch_array($resultx);
							$l2=stripslashes($rowx['lower']);//10164
							$u2=stripslashes($rowx['upper']);//19740
							$t2=stripslashes($rowx['tax']);//15
							
							$resultx =mysqli_query($db,"select * from tax where id=3");
							$rowx=mysqli_fetch_array($resultx);
							$l3=stripslashes($rowx['lower']);
							$u3=stripslashes($rowx['upper']);
							$t3=stripslashes($rowx['tax']);
							
							$resultx =mysqli_query($db,"select * from tax where id=4");
							$rowx=mysqli_fetch_array($resultx);
							$l4=stripslashes($rowx['lower']);
							$u4=stripslashes($rowx['upper']);
							$t4=stripslashes($rowx['tax']);
							
							$resultx =mysqli_query($db,"select * from tax where id=5");
							$rowx=mysqli_fetch_array($resultx);
							$l5=stripslashes($rowx['lower']);
							$t5=stripslashes($rowx['tax']);
							
							
							
							
							$tax=0;$a=0;
						
							if($taxnet<$u1){
							$tax=$t1*$taxnet;
							}
								
							else if(($taxnet>=$l2)&&($taxnet<=$u2)){
							$tax+=0.1*$l2;
							$taxnet-=$l2;
							$a=$taxnet*$t2;
							$tax+=$a;
							
							}
							
							else if(($l3<=$taxnet&&$taxnet<=$u3)){
							$tax+=0.1*$l2;
							$tax+=$t2*($u2-$l2);
							$taxnet-=$u2;
							$a=$taxnet*$t3;
							$tax+=$a;
							}
							
							else if(($l4<=$taxnet&&$taxnet<=$u4)){
							$tax+=0.1*$l2;
							$tax+=$t2*($u2-$l2);
							$tax+=$t3*($u3-$u2);
							$taxnet-=$u3;
							$a=$taxnet*$t4;
							$tax+=$a;
							}
							
							else if(($taxnet>$l5)){
							$tax+=0.1*$l2;
							$tax+=$t2*($u2-$l2);
							$tax+=$t3*($u3-$u2);
							$tax+=$t4*($u4-$u3);
							$taxnet-=$l5;
							$a=$taxnet*$t5;
							$tax+=$a;
							}
							else{}
							
							
							
							if($tax>1162){
							$tax=$tax-1162;
							}
							$tax=round($tax,2);
						//deduct nhif,tax,deductions,insurance,advance,scont,sloan
		$net=$net-$nhif-$ded-$ins-$adva-$tax;
		$net=round($net,2);
		
		$resulta = mysqli_query($db,"update payroll set sal='".$sal."',
		allow='".$allow."',adva='".$adva."',ins='".$ins."',ded='".$ded."',tax='".$tax."',nhif='".$nhif."',nssf='".$nssf."',nhif='".$nhif."',rateot='".$rateot."',othrs='".$othrs."',totalot='".$totalot."',net='".$net."', status=1 where emp='".$emp."' and month='".$mon."'");
		
		if($resulta){
	$resulta = mysqli_query($db,"insert into log values('','".$username." edits payroll.Month:".$mon.";Emp id:".$emp."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
	echo"<script>
	$('#sal' + '".$emp."').val('".$sal."');
	$('#nhif' + '".$emp."').val('".$nhif."');
	$('#nssf' + '".$emp."').val('".$nssf."');
	$('#tax' + '".$emp."').val('".$tax."');   
	$('#rate' + '".$emp."').val('".$rateot."');
	$('#ot' + '".$emp."').val('".$totalot."');
	$('#net' + '".$emp."').val('".$net."');
	</script>";
									echo'<img src="images/tick.png"  width="22" height="22"/>';
									
									}
								else{
									echo'<img src="images/delete.png"  width="21.5" height="21.5"/>';
									}
								break;
								
							case '36.2':
							$mon=$_GET['mon'];
							$emp=$_GET['emp'];
									
									$query =mysqli_query($db,"select * from salregister where month='".$mon."' and status=1");
									$count = mysqli_num_rows($query);
									if($count==0){
									echo"<script>
									$('#empdi').hide();
									$().customAlert();
									alert('Error!', '<p>Payroll for Month does not exist or has already been posted.</p>');
									e.preventDefault();
									</script>";	
										exit;
									}
									
									$query =mysqli_query($db,"select * from payroll where month='".$mon."' and emp='".$emp."'");
									$count = mysqli_num_rows($query);
									if($count>0){
									echo"<script>
									$('#empdi').hide();
									$().customAlert();
									alert('Error!', '<p>Employee already exists in the payroll.</p>');
									e.preventDefault();
									</script>";	
									exit;	
									}
									else{
						$resultb =mysqli_query($db,"select * from employee where emp='".$emp."'");
								$rowb=mysqli_fetch_array($resultb);
								$emp=stripslashes($rowb['emp']);
								$leave=stripslashes($rowb['leaveac']);
								$names=stripslashes($rowb['fname']).' '.stripslashes($rowb['mname']).' '.stripslashes($rowb['lname']);	
								
								$q=0;
								$resultx =mysqli_query($db,"select * from ".$mon." where pfno='".$emp."'");
								$rowx=mysqli_fetch_array($resultx);
					
								for ($x=1; $x<32; $x++) {
											$d=sprintf("%02d",$x);
											$d=$d.'c';
											if(stripslashes($rowx[$d])==1||stripslashes($rowx[$d])==2||stripslashes($rowx[$d])==3){
												$q++;
											}
								}
								
										
				$result =mysqli_query($db,"select * from payroll where status=1 and emp='".$emp."' order by serial desc limit 0,1");
				$row=mysqli_fetch_array($result);
														
$resultc = mysqli_query($db,"insert into payroll values('','".$mon."','".stripslashes($rowb['emp'])."','".$names."','".stripslashes($rowb['dept'])."','".stripslashes($row['sal'])."','".stripslashes($row['allow'])."','','','','','','','','','','',0,'".stripslashes($rowb['bid'])."','".stripslashes($rowb['bname'])."','".stripslashes($rowb['acno'])."','".$q."')");				
										
	
									if($result){	
	$resulta = mysqli_query($db,"insert into log values('','".$username." inserts new employee into payroll.Month:".$mon."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
									echo'<p><img src="images/tick.png" style="width:30px; height:30px; margin-top:-5px"></p>';
									echo "<script>
									$('#mon').hide();payroll('".$mon."');</script>";
										
									}
									
									}
							break;
							case '37.2':
							$mon=$_GET['mon'];
							$date=date('Y/m/d');
							$stamp=date('Ymd');
							$query =mysqli_query($db,"select * from salregister where month='".$mon."' and status=1");
									$count = mysqli_num_rows($query);
									if($count==0){
									echo"<script>
									$$().customAlert();
									alert('Error!', '<p>Payroll for Month does not exist or has already been posted.</p>');
									e.preventDefault();
									</script>";	
										exit;
									}
									else{
							$totalnet=0;$totalsal=0;$totalallow=0;$totaladva=0;$totalins=0;$totalded=0;$totalothrs=0;$totalotal=0;$totalnssf=0;$totalnhif=0;$totaltax=0;
							$result =mysqli_query($db,"select * from payroll where month='".$mon."'");
							$num_results = mysqli_num_rows($result);
							for ($i=0; $i <$num_results; $i++) {
							$row=mysqli_fetch_array($result);
							$net=stripslashes($row['net']);
							$sal=stripslashes($row['sal']);
							$allow=stripslashes($row['allow']);
							$adva=stripslashes($row['adva']);
							$ins=stripslashes($row['ins']);
							$ded=stripslashes($row['ded']);
							$othrs=stripslashes($row['othrs']);
							$totalot=stripslashes($row['totalot']);
							$nssf=stripslashes($row['nssf']);
							$nhif=stripslashes($row['nhif']);
							$tax=stripslashes($row['tax']);
							$totalnet+=$net;
							$totalsal+=$sal;
							$totalallow+=$allow;
							$totaladva+=$adva;
							$totalins+=$ins;
							$totalded+=$ded;
							$totalothrs+=$othrs;
							$totalotal+=$totalot;
							$totalnssf+=$nssf;
							$totalnhif+=$nhif;
							$totaltax+=$tax;
							
							if(stripslashes($row['status'])==0){
							echo"<script>
									$().customAlert();
									alert('Error!', '<p>Details of ".stripslashes($row['name'])." have not been saved yet!</p>');
									e.preventDefault();
									</script>";	
								exit;	
							}
							
							
							}
							
							
												
		$resultb = mysqli_query($db,"update salregister set amount='".$totalnet."',date='".$date."',stamp='".$stamp."',sal='".$totalsal."',allow='".$totalallow."',adva='".$totaladva."',nssf='".$totalnssf."',nhif='".$totalnhif."',tax='".$totaltax."',ins='".$totalins."',ded='".$totalded."',othrs='".$totalothrs."',totalot='".$totalotal."',status=0 where month='".$mon."'");
							
							if($resultb){	
	$resulta = mysqli_query($db,"insert into log values('','".$username." commits payroll.Month:".$mon."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
									echo"<script>
									$().customAlert();
									alert('Success!', '<p>Payroll Submitted!</p>');
									e.preventDefault();
									</script>";	
										
									}
									
									}
								break;
								
								case '38.2':
								$code=$_GET['code'];
									$query =mysqli_query($db,"select * from nhif where id='".$code."'");
									$count = mysqli_num_rows($query);
									
									if($count>0){
										
							$result = mysqli_query($db,"update nhif set lower='".$_GET['lower']."',upper='".$_GET['upper']."',amount='".$_GET['amount']."' where id='".$code."'");	
									}
									else{
										
								$result= mysqli_query($db,"insert into nhif values('".$code."','".$_GET['lower']."','".$_GET['upper']."','".$_GET['amount']."',1)");	
								}
								if($result){
	$resulta = mysqli_query($db,"insert into log values('','".$username." edits nhif table.id:".$code."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
						echo'<img src="images/tick.png" width="22px" height="22px"/>';
								}
								else{
									echo'<img src="images/tick.png" width="22px" height="22px"/>';
									}
							break;
							
							case '39.2':
							$code=$_GET['code'];
							$result = mysqli_query($db,"update tax set lower='".$_GET['lower']."',upper='".$_GET['upper']."',tax='".$_GET['amount']."' where id='".$code."'");	
								
								if($result){
	$resulta = mysqli_query($db,"insert into log values('','".$username." edits tax table.id:".$code."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
						echo'<img src="images/tick.png" width="22px" height="22px"/>';
								}
								else{
									echo'<img src="images/tick.png" width="22px" height="22px"/>';
									}
							break;
							
							case '40.2':
								$resulta = mysqli_query($db,"update nssf set amount='".$_GET['employee']."' where id=1");	
							if($resulta){
	$resulta = mysqli_query($db,"insert into log values('','".$username." edits nssf table','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
						echo'<img src="images/tick.png" width="22px" height="22px"/>';
								}
								else{
									echo'<img src="images/tick.png" width="22px" height="22px"/>';
									}
							break;
							case '41.2':
							$resulta = mysqli_query($db,"update overtime set rate='".$_GET['amount']."' where id=1");	
							if($resulta){
	$resulta = mysqli_query($db,"insert into log values('','".$username." edits overtime table','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
						echo'<img src="images/tick.png" width="22px" height="22px"/>';
								}
								else{
									echo'<img src="images/tick.png" width="22px" height="22px"/>';
									}
							break;
							
							case '29.6':
						$result= mysqli_query($db,"insert into wardbeds values('','".$_GET['ward']."','".$_GET['roomno']."','".$_GET['roomtype']."','".$_GET['bedno']."','','',0)");
							$resulta = mysqli_query($db,"insert into log values('','".$username." inserts data into wardbeds database.Bed No:".$_GET['bedno']."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
					
							if($result){
									echo'<p><img src="images/tick.png" style="width:30px; height:30px"></p>';
									echo "<script>paginate(12,0);</script>";
									}
									else{
										echo'<p><img src="images/delete.png" style="width:30px; height:30px"></p>';
										}
							
							break;	
							case '30.6':
							$code=$_GET['code'];
							$result = mysqli_query($db,"DELETE from wardbeds where id=".$code."");
							$resulta = mysqli_query($db,"insert into log values('','".$username." deletes data from wardbeds database.Bed Id:".$code."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	

							break;
							case '31.6':
							$code=$_GET['code'];
						$result = mysqli_query($db,"update wardbeds set type='".$_GET['roomtype']."',roomno='".$_GET['roomno']."',wardtype='".$_GET['ward']."' where id='".$code."'");	
							$resulta = mysqli_query($db,"insert into log values('','".$username." updates ward beds database.Bed Id:".$code."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
							if($result){
									echo'<p><img src="images/tick.png" style="width:16px; height:16px; margin-top:-10px; margin-left:20px"></p>';
									}
									else{
										echo'<p><img src="images/delete.png" style="width:16px; height:16px; margin-top:0px"></p>';
									}
							break;
							
							case 87:
							$itcode=$_GET['itcode'];
							$qty=$_GET['qty'];
							$notes=$_GET['notes'];
							$date=date('Y/m/d');
							$stamp=date('Ymd');
							$result =mysqli_query($db,"select * from items where ItemCode='".$itcode."'");
							$row=mysqli_fetch_array($result);
							$bal=stripslashes($row['Bal']);
							$itname=stripslashes($row['ItemName']);
							$sprice=stripslashes($row['SalePrice']);
							$pack=stripslashes($row['Pack']);
							$nbal=$bal-$qty;
							$total=$sprice*$nbal;
							
							//insert into stock track
		$resultd = mysqli_query($db,"insert into stocktrack values('','".$date."','".$username."','".$itcode."','".$itname."','".$pack."','STOCK USAGE REGISTER','".$qty."','".$nbal."','".$username."','".$stamp."')");	
			
							$resultb = mysqli_query($db,"update items set Bal='".$nbal."' where ItemCode='".$itcode."'");
							$resulta = mysqli_query($db,"insert into stockuse values('','".$itcode."','".$itname."','".$qty."','".$notes."','".date('d/m/Y')."','".date('Ymd')."','".$username."',1)");	
							if($resulta&&$resultb){
								
			//update ledgers-stock
			$amount=$total;
			$resultb = mysqli_query($db,"select * from ledgers where ledgerid='630'");
					$rowb=mysqli_fetch_array($resultb);
					$invbal=stripslashes($rowb['bal']);
					$invbal=$invbal+$amount;
					
					$resultc = mysqli_query($db,"select * from ledgers where ledgerid='651'");
					$rowc=mysqli_fetch_array($resultc);
					$supbal=stripslashes($rowc['bal']);
					$supbal=$supbal+$amount;
					
			$resultl = mysqli_query($db,"insert into ledgerentries values('','651','Supplies Revenue','630','Inventory','".$amount."','Stock Usage','".$date."','".$supbal."','".$invbal."','".$stamp."','','',0)");
			$resultm = mysqli_query($db,"update ledgers set bal='".$invbal."' where ledgerid='630'");
			$resultn = mysqli_query($db,"update ledgers set bal='".$supbal."' where ledgerid='651'");
			$resulta = mysqli_query($db,"insert into log values('','".$username." makes an entry in the stock usage database.','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
									echo"
										<script>
											setTimeout(function() {
										stockusage();},500);
										$().customAlert();
										alert('Success!', '<p>Entry Posted.</p>');
										e.preventDefault();
									</script>";
										
									}
									else{
										echo"
										<script>
										$().customAlert();
										alert('Sorry!', '<p>Please Try again.</p>');
										e.preventDefault();
										</script>";
									}
							break;
								
							case 90:
							$stamp=date('Ymd');
							
							//get receipt no
							$question =mysqli_query($db,"SELECT * FROM goodsreturned order by id desc limit 0,1");
							$ans=mysqli_fetch_array($question);
							$rcptno=stripslashes($ans['gnrno'])+1;
							
							$fintot=$_GET['fintot'];$totalsale=0;
							$max=count($_SESSION['ret']);
							for ($i = 0; $i < $max; $i++){
							$code=$_SESSION['ret'][$i][0];
							$name=$_SESSION['ret'][$i][1];
							$unit=$_SESSION['ret'][$i][2];
							$part=$_SESSION['ret'][$i][3];
							$pprice=$_SESSION['ret'][$i][4];
							$date=$_SESSION['ret'][$i][5];
							$total=$_SESSION['ret'][$i][6];
							$sname=$_SESSION['ret'][$i][7];
							$batch=$_SESSION['ret'][$i][8];
							$invoice=$_SESSION['ret'][$i][9];
							$expiry=$_SESSION['ret'][$i][10];
							$lpo=$_SESSION['ret'][$i][11];
							$reason=$_SESSION['ret'][$i][12];
							$pack=$_SESSION['ret'][$i][13];
							$bal=$_SESSION['ret'][$i][14];
							$qty=$_SESSION['ret'][$i][15];
							$totalsale+=$_SESSION['ret'][$i][16];
							$sid=$_SESSION['ret'][$i][17];
							$diff=$bal-$qty;
							
							//insert into stock track
		$resultd = mysqli_query($db,"insert into stocktrack values('','".date('Y/m/d')."','PROCUREMENT','".$code."','".$name."','".$pack."','GOODS RETURNED-".$sname."','".$qty."','".$diff."','".$username."','".$stamp."')");	
		
							$resultb= mysqli_query($db,"update items set Bal='".$diff."' where ItemCode='".$code."'");
							$resulta = mysqli_query($db,"insert into goodsreturned values('','".$rcptno."','".$code."','".$name."','".$unit."','".$part."','".$pprice."','".$date."','".$total."','".$sname."','".$batch."','".$invoice."','".$expiry."','".$lpo."','".$reason."','".$pack."','".date('Ymd')."','".$username."',1,'".$totalsale."')");	
								}
				if($resulta&&$resultb){
$resulta = mysqli_query($db,"insert into log values('','".$username." returns goods.Rcpt No.".$rcptno."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	




//update ledgers-stock
			$amount=$totalsale;
			$resultb = mysqli_query($db,"select * from ledgers where ledgerid='630'");
					$rowb=mysqli_fetch_array($resultb);
					$invbal=stripslashes($rowb['bal']);
					$invbal=$invbal-$amount;
					
					$resultc = mysqli_query($db,"select * from ledgers where ledgerid='651'");
					$rowc=mysqli_fetch_array($resultc);
					$supbal=stripslashes($rowc['bal']);
					$supbal=$supbal-$amount;
					
			$resultl = mysqli_query($db,"insert into ledgerentries values('','651','Supplies Revenue','630','Inventory','".$amount."','Goods Returned Outwards','".$date."','".$supbal."','".$invbal."','".$stamp."','','',0)");
			$resultm = mysqli_query($db,"update ledgers set bal='".$invbal."' where ledgerid='630'");
			$resultn = mysqli_query($db,"update ledgers set bal='".$supbal."' where ledgerid='651'");
			
			
			
			//update ledgers-acs/payable
					$amount=$fintot;
					$resultb = mysqli_query($db,"select * from ledgers where ledgerid='629'");
					$rowb=mysqli_fetch_array($resultb);
					$acbal=stripslashes($rowb['bal']);
					$acbal=$acbal-$amount;
					
					$resultc = mysqli_query($db,"select * from ledgers where ledgerid='644'");
					$rowc=mysqli_fetch_array($resultc);
					$costbal=stripslashes($rowc['bal']);
					$costbal=$costbal-$amount;
					
			$resultl = mysqli_query($db,"insert into ledgerentries values('','644','Cost of Goods Sold','629','Accounts Payable','".$amount."','Goods Returned Outwards','".$date."','".$costbal."','".$acbal."','".$stamp."','','',0)");
			$resultm = mysqli_query($db,"update ledgers set bal='".$acbal."' where ledgerid='629'");
			$resultn = mysqli_query($db,"update ledgers set bal='".$costbal."' where ledgerid='644'");
			

//post credit note
$resultc =mysqli_query($db,"SELECT * FROM creditsuppliers WHERE CustomerId='".$sid."'");
$rowc=mysqli_fetch_array($resultc);
$bal2=stripslashes($rowc['Bal']);
$bal3=$bal2-$amount;										
$resulta = mysqli_query($db,"insert into supplierdebts values('','".$sid."','".$sname."','".$invoice."','".$rcptno."','".$amount."','cr','0','".$bal3."','".$bal3."','Purchases','".date('d/m/Y')."','".$stamp."',1)");
$resultn = mysqli_query($db,"update creditsuppliers set Bal='".$bal3."' where CustomerId='".$sid."'");


unset($_SESSION['ret']);
				echo'<embed src="images/honk3.wav" autostart="true" width="0" height="0" id="sound1" enablejavascript="true">';	
					echo"<script>		
										window.open('report.php?id=51&rcptno=".$rcptno."');
										setTimeout(function() {
											returnout();},500);
										$().customAlert();
										alert('Success!', '<p>Transaction Successful.</p>');
										e.preventDefault();
										</script>";
							exit;
									}
									else{
										echo"
										<script>
										$().customAlert();
										alert('Sorry!', '<p>Transaction Failure.Please Repeat the transaction.</p>');
										e.preventDefault();
										</script>";
									}
							
							break;
							
								case 91:
							$stamp=date('Ymd');
							
							//get receipt no
							$question =mysqli_query($db,"SELECT * FROM lpo order by id desc limit 0,1");
							$ans=mysqli_fetch_array($question);
							$rcptno=stripslashes($ans['lpono'])+1;
							
							
							$max=count($_SESSION['lpo']);
							for ($i = 0; $i < $max; $i++){
							$code=$_SESSION['lpo'][$i][0];
							$name=$_SESSION['lpo'][$i][1];
							$unit=$_SESSION['lpo'][$i][2];
							$part=$_SESSION['lpo'][$i][3];
							$pprice=$_SESSION['lpo'][$i][4];
							$date=$_SESSION['lpo'][$i][5];
							$total=$_SESSION['lpo'][$i][6];
							$sname=$_SESSION['lpo'][$i][7];
							$pack=$_SESSION['lpo'][$i][8];
							
							$resulta = mysqli_query($db,"insert into lpo values('','".$rcptno."','".$sname."','".$date."','".$name."','".$pack."','".$unit."','".$part."','".$pprice."','".$total."','".date('Ymd')."','".$username."',1)");	
								}
				if($resulta){
$resulta = mysqli_query($db,"insert into log values('','".$username." prepares lpo.Rcpt No.".$rcptno."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
unset($_SESSION['lpo']);
				echo'<embed src="images/honk3.wav" autostart="true" width="0" height="0" id="sound1" enablejavascript="true">';	
					echo"<script>		
										window.open('report.php?id=52&rcptno=".$rcptno."');
										setTimeout(function() {
											lpo();},500);
										$().customAlert();
										alert('Success!', '<p>Transaction Successful.</p>');
										e.preventDefault();
										</script>";
							exit;
									}
									else{
										echo"
										<script>
										$().customAlert();
										alert('Sorry!', '<p>Transaction Failure.Please Repeat the transaction.</p>');
										e.preventDefault();
										</script>";
									}
							
							break;
							
							case 92:
							$nbal=$_GET['nbal'];
							$total=$_GET['total'];
							$lid=$_GET['lid'];
							$lname=$_GET['lname'];
							$stamp=date('Ymd');
							$date=date('Y/m/d');
							$max=count($_SESSION['credinvoice']);
							foreach ($_SESSION['credinvoice'] as $i => $val) {
							$code=$_SESSION['credinvoice'][$i][0];
							$amount=$_SESSION['credinvoice'][$i][1];
							$cid=$_SESSION['credinvoice'][$i][2];
							$cname=$_SESSION['credinvoice'][$i][3];
							$invno=$_SESSION['credinvoice'][$i][4];
							$bal=$_SESSION['credinvoice'][$i][5];
							$paying=$_SESSION['credinvoice'][$i][6];
							$invbal=$_SESSION['credinvoice'][$i][7];
							$paid=$_SESSION['credinvoice'][$i][8];
							$sbal=$amount-$paying;
							$npaid=$paid+$paying;
							$ninvbal=$invbal-$paying;
							
						$resulta = mysqli_query($db,"insert into customerdebts values('','".$cid."','".$cname."','".$invno."','".$paying."','cr','".$npaid."','".$ninvbal."','".$bal."','Payment of Invoice-Inv No-".$invno."','".date('Y/m/d')."','".date('Ymd')."',1)");
						$resultb = mysqli_query($db,"update customerdebts set InvBal='".$ninvbal."',Paid='".$npaid."' where TransNo='".$code."'");
						if($ninvbal==0){
						$resultc = mysqli_query($db,"update customerdebts set Status=2 where TransNo='".$code."'");
						}
						
						
								}
				if($resulta){
$result = mysqli_query($db,"update creditcustomers set Bal='".$nbal."' where CustomerId='".$cid."'");	

								//update ledger
								$resultf = mysqli_query($db,"select * from ledgers where ledgerid=628");
								$row=mysqli_fetch_array($resultf);
								$bal=stripslashes($row['bal']);
								$balc=$bal-$total;
								
								$resultg = mysqli_query($db,"select * from ledgers where ledgerid=".$lid."");
								$row=mysqli_fetch_array($resultg);
								$bal=stripslashes($row['bal']);
								$bald=$bal+$total;
								
								
								$resulte = mysqli_query($db,"insert into ledgerentries values('','".$lid."','".$lname."','628','Accounts Receivable','".$total."','Income from Credit sales','".$date."','".$bald."','".$balc."','".$stamp."',0)");
								$resultf = mysqli_query($db,"update ledgers set bal='".$balc."' where ledgerid=628");
								$resultg = mysqli_query($db,"update ledgers set bal='".$bald."' where ledgerid=".$lid."");
								
								
$resulta = mysqli_query($db,"insert into log values('','".$username." receives payment from debtor.Invoice No.".$invno."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
unset($_SESSION['credinvoice']);
				echo'<embed src="images/honk3.wav" autostart="true" width="0" height="0" id="sound1" enablejavascript="true">';	
					echo"<script>	$('#total').val('');	$('#bal').val('');
										$().customAlert();
										alert('Success!', '<p>Transaction Successful.</p>');
											$.ajax({
											url:'link.php',
											data:{id:22,cus:'".$cid."'},
											success:function(data){
											$('#display').html(data);
											}
										});
										
										
										</script>";
							exit;
									}
									else{
										echo"
										<script>
										$().customAlert();
										alert('Sorry!', '<p>Transaction Failure.Please Repeat the transaction.</p>');
										e.preventDefault();
										</script>";
									}
							
							break;
							
								case 94:
							$nbal=$_GET['nbal'];
							$total=$_GET['total'];
							$lid=$_GET['lid'];
							$lname=$_GET['lname'];
							$stamp=date('Ymd');
							$date=date('Y/m/d');
							
							$max=count($_SESSION['supinvoice']);
							foreach ($_SESSION['supinvoice'] as $i => $val) {
							$code=$_SESSION['supinvoice'][$i][0];
							$amount=$_SESSION['supinvoice'][$i][1];
							$cid=$_SESSION['supinvoice'][$i][2];
							$cname=$_SESSION['supinvoice'][$i][3];
							$invno=$_SESSION['supinvoice'][$i][4];
							$bal=$_SESSION['supinvoice'][$i][5];
							$paying=$_SESSION['supinvoice'][$i][6];
							$invbal=$_SESSION['supinvoice'][$i][7];
							$paid=$_SESSION['supinvoice'][$i][8];
							$sbal=$amount-$paying;
							$npaid=$paid+$paying;
							$ninvbal=$invbal-$paying;
							
							
							
	$resulta = mysqli_query($db,"insert into supplierdebts values('','".$cid."','".$cname."','".$invno."','','".$paying."','cr','".$npaid."','".$ninvbal."','".$bal."','Payment of GRN Invoice-".$invno."','".date('d/m/Y')."','".date('Ymd')."',1)");
						$resultb = mysqli_query($db,"update supplierdebts set InvBal='".$ninvbal."',Paid='".$npaid."' where TransNo='".$code."'");
						if($ninvbal==0){
						$resultc = mysqli_query($db,"update supplierdebts set Status=2 where TransNo='".$code."'");
						}
						
						}
				if($resulta){
$result = mysqli_query($db,"update creditsuppliers set Bal='".$nbal."' where CustomerId='".$cid."'");	

								//update ledger
								$resultf = mysqli_query($db,"select * from ledgers where ledgerid=629");
								$row=mysqli_fetch_array($resultf);
								$bal=stripslashes($row['bal']);
								$balc=$bal-$total;
								
								$resultg = mysqli_query($db,"select * from ledgers where ledgerid=".$lid."");
								$row=mysqli_fetch_array($resultg);
								$bal=stripslashes($row['bal']);
								$bald=$bal-$total;
								
								
								$resulte = mysqli_query($db,"insert into ledgerentries values('','629','Accounts Payable','".$lid."','".$lname."','".$total."','Payment of Creditors','".$date."','".$balc."','".$bald."','".$stamp."',0)");
								$resultf = mysqli_query($db,"update ledgers set bal='".$balc."' where ledgerid=629");
								$resultg = mysqli_query($db,"update ledgers set bal='".$bald."' where ledgerid=".$lid."");
								
								
$resulta = mysqli_query($db,"insert into log values('','".$username." makes payment to creditor.Invoice No.".$invno."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
unset($_SESSION['supinvoice']);
				echo'<embed src="images/honk3.wav" autostart="true" width="0" height="0" id="sound1" enablejavascript="true">';	
					echo"<script>	$('#total').val('');	$('#bal').val('');
										$().customAlert();
										alert('Success!', '<p>Transaction Successful.</p>');
											$.ajax({
											url:'link.php',
											data:{id:31,cus:'".$cid."'},
											success:function(data){
											$('#display').html(data);
											}
										});
										
										
										</script>";
							exit;
									}
									else{
										echo"
										<script>
										$().customAlert();
										alert('Sorry!', '<p>Transaction Failure.Please Repeat the transaction.</p>');
										e.preventDefault();
										</script>";
									}
							
							break;
							
							case '115':
								
							$dr=$_GET['dr'];
							$drname=$_GET['drname'];
							$desc=$_GET['desc'];
							$amount=$_GET['amount'];
							$date=date('Y/m/d');
							$stamp=date('Ymd');
							
						$result =mysqli_query($db,"select * from ledgers  where ledgerid=658");
						$row=mysqli_fetch_array($result);
						$cr=stripslashes($row['ledgerid']);
						$crname=stripslashes($row['name']);
						$balc=stripslashes($row['bal']);
						
						
						//expense account	
						$resulta = mysqli_query($db,"select * from ledgers where ledgerid=".$dr."");
						$row=mysqli_fetch_array($resulta);
						$bald=stripslashes($row['bal']);
						
						$balc=$balc-$amount;
						$bald=$bald+$amount;
						if($balc<0){
								echo"<script>
									$().customAlert();
									alert('Error!', '<p>Petty Cash Account balance cannot be less than zero.</p>');
									e.preventDefault();
									</script>";
									exit;
						}
						
						
						
						
			$resultb = mysqli_query($db,"insert into ledgerentries values('','".$cr."','".$crname."','".$dr."','".$drname."','".$amount."','".$desc."','".$date."','".$balc."','".$bald."','".$stamp."',1)");
			$resulte = mysqli_query($db,"update ledgers set bal='".$balc."' where ledgerid='".$cr."'");
			$resultf = mysqli_query($db,"update ledgers set bal='".$bald."' where ledgerid='".$dr."'");
			
			if($resultb&&$resulte&&$resultf){
			$resulta = mysqli_query($db,"insert into log values('','".$username." makes an expenses management entry. Expense Account:".$drname."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
								echo'<p><img src="images/tick.png" style=" width:30px; height:30px; margin:-10px 0 0 0px"></p>';
								echo"<script>
								setTimeout(function() {
											expman();},500);
									</script>";
									
									}
								else{
									echo'<p><img src="images/delete.png" style=" width:30px; height:30px; margin:-10px 0 0 0px"></p>';
									}
							break;
								
							case '116':
							$dr=$_GET['dr'];
							$drname=$_GET['drname'];
							$desc=$_GET['desc'];
							$action=$_GET['action'];
							$amount=$_GET['amount'];
							$date=date('Y/m/d');
							$stamp=date('Ymd');
							
						if($action=='Cash Deposit'){
						
						//cash account	
						$result =mysqli_query($db,"select * from ledgers  where ledgerid=625");
						$row=mysqli_fetch_array($result);
						$cr=stripslashes($row['ledgerid']);
						$crname=stripslashes($row['name']);
						$balc=stripslashes($row['bal']);
						
						//bank account	
						$resulta = mysqli_query($db,"select * from ledgers where ledgerid=".$dr."");
						$row=mysqli_fetch_array($resulta);
						$bald=stripslashes($row['bal']);
						
						$balc=$balc-$amount;
						$bald=$bald+$amount;
						
						$resultb = mysqli_query($db,"insert into ledgerentries values('','".$cr."','".$crname."','".$dr."','".$drname."','".$amount."','".$desc."','".$date."','".$balc."','".$bald."','".$stamp."',1)");
							
						}
						
						if($action=='Cash Withdrawal'){
						
						//cash account	
						$result =mysqli_query($db,"select * from ledgers  where ledgerid=625");
						$row=mysqli_fetch_array($result);
						$cr=stripslashes($row['ledgerid']);
						$crname=stripslashes($row['name']);
						$balc=stripslashes($row['bal']);
						
						//bank account	
						$resulta = mysqli_query($db,"select * from ledgers where ledgerid=".$dr."");
						$row=mysqli_fetch_array($resulta);
						$bald=stripslashes($row['bal']);
						
						$balc=$balc+$amount;
						$bald=$bald-$amount;
						
						$resultb = mysqli_query($db,"insert into ledgerentries values('','".$dr."','".$drname."','".$cr."','".$crname."','".$amount."','".$desc."','".$date."','".$bald."','".$balc."','".$stamp."',1)");
							
						}
						
						if($action=='Cheque Deposit'){
						
						//cheques account	
						$result =mysqli_query($db,"select * from ledgers  where ledgerid=659");
						$row=mysqli_fetch_array($result);
						$cr=stripslashes($row['ledgerid']);
						$crname=stripslashes($row['name']);
						$balc=stripslashes($row['bal']);
						
						//bank account	
						$resulta = mysqli_query($db,"select * from ledgers where ledgerid=".$dr."");
						$row=mysqli_fetch_array($resulta);
						$bald=stripslashes($row['bal']);
						
						$balc=$balc+$amount;
						$bald=$bald+$amount;
						
						$resultb = mysqli_query($db,"insert into ledgerentries values('','".$cr."','".$crname."','".$dr."','".$drname."','".$amount."','".$desc."','".$date."','".$balc."','".$bald."','".$stamp."',1)");
							
						}
						
						if($action=='Cheque Payment'){
						
						//cheques account	
						$result =mysqli_query($db,"select * from ledgers  where ledgerid=659");
						$row=mysqli_fetch_array($result);
						$cr=stripslashes($row['ledgerid']);
						$crname=stripslashes($row['name']);
						$balc=stripslashes($row['bal']);
						
						//bank account	
						$resulta = mysqli_query($db,"select * from ledgers where ledgerid=".$dr."");
						$row=mysqli_fetch_array($resulta);
						$bald=stripslashes($row['bal']);
						
						$balc=$balc-$amount;
						$bald=$bald-$amount;
						
						$resultb = mysqli_query($db,"insert into ledgerentries values('','".$dr."','".$drname."','".$cr."','".$crname."','".$amount."','".$desc."','".$date."','".$bald."','".$bald."','".$stamp."',1)");
							
						}
						
						
						
						
			
			$resulte = mysqli_query($db,"update ledgers set bal='".$balc."' where ledgerid='".$cr."'");
			$resultf = mysqli_query($db,"update ledgers set bal='".$bald."' where ledgerid='".$dr."'");
			
			if($resultb&&$resulte&&$resultf){
			$resulta = mysqli_query($db,"insert into log values('','".$username." makes a bank ".$action.". Bank Account:".$drname."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
								echo'<p><img src="images/tick.png" style=" width:30px; height:30px; margin:-10px 0 0 0px"></p>';
								echo"<script>
								setTimeout(function() {
											bankdep();},500);
									</script>";
									
									}
								else{
									echo'<p><img src="images/delete.png" style=" width:30px; height:30px; margin:-10px 0 0 0px"></p>';
									}
								break;
								
							case 117:
							$a=$_GET['a'];
							$regn=$_GET['regn'];
							$name=strtoupper($_GET['name']);
							$vname=strtoupper($_GET['vname']);
							$dop=$_GET['dop'];
							$dispdate=$_GET['dispdate'];
							$asscat=$_GET['asscat'];
							$catname=$_GET['catname'];
							$location=$_GET['location'];
							$deprate=$_GET['deprate'];
							$price=$_GET['price'];
							$odetail=$_GET['odetail'];
							$date=date('Y/m/d');
							$stamp=date('Ymd');
							
							
							
			if($a==1){	
			$resulta = mysqli_query($db,"select * from assets where assetid='".$regn."'");
			$num_resultsa = mysqli_num_rows($resulta);	
			if($num_resultsa!=0){
				echo"
										<script>
										$().customAlert();
										alert('Error!', '<p>Asset ID already exists.</p>');
										e.preventDefault();
										</script>";
										exit;
			}
					
		$resulta = mysqli_query($db,"insert into assets values('','".$regn."','".$name."','".$asscat."','".$catname."','".$vname."','".$dop."','".$dispdate."','".$location."','".$price."','".$deprate."','".$odetail."','".$date."','".$stamp."',1)");
			$resultx = mysqli_query($db,"insert into assettrack values('','".$regn."','".$name."','New Asset','New Asset Registration','".$price."','".$date."','".$stamp."',1,'".$username."')");
			}else{
	$resulta = mysqli_query($db,"update assets set assetid='".$regn."',name='".$name."',subcatid='".$asscat."',category='".$catname."',vendor='".$vname."',dateofpurchase='".$dop."',disposaldate='".$dispdate."',location='".$location."',price='".$price."',deprate='".$deprate."',odetail='".$odetail."' where assetid=".$regn."");
		$resultx = mysqli_query($db,"insert into assettrack values('','".$regn."','".$name."','Edit Asset','Asset editing','".$price."','".$date."','".$stamp."',1,'".$username."')");
			}
								
							
			if($resulta){
	$resulta = mysqli_query($db,"insert into log values('','".$username." updates assets database.Asset No:".$regn."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
echo"<img src=\"images/tick.png\" style=\"\"  width=\"30\" height=\"30\"/>";
if($a==1){
echo"<script>setTimeout(function() {addasset();},500);</script>";	
}else{
	echo"<script>setTimeout(function() {editasset(75);},500);</script>";	
}
								
							}
							else {echo"<img src=\"images/delete.png\" style=\"\"  width=\"30\" height=\"30\"/>";}
							
							break;
							
							case 118:
							$regn=$_GET['regn'];
							$name=strtoupper($_GET['name']);
							$doa=$_GET['doa'];
							$dor=$_GET['dor'];
							$assby=$_GET['assby'];
							$assto=$_GET['assto'];
							$location=$_GET['location'];
							$price=$_GET['price'];
							$odetail=$_GET['odetail'];
							$date=date('Y/m/d');
							$stamp=date('Ymd');
							
		
					
		$resulta = mysqli_query($db,"insert into assignments values('','".$regn."','".$name."','".$price."','".$location."','".$assby."','".$assto."','".$doa."','".$dor."','','".$odetail."','".$date."','".$stamp."',1,'".$username."')");
		$resultb= mysqli_query($db,"update assets set status=2 where assetid='".$regn."'");
			
			if($resulta){
	$resulta = mysqli_query($db,"insert into log values('','".$username." assigns asset.Asset No:".$regn."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
		$resultx = mysqli_query($db,"insert into assettrack values('','".$regn."','".$name."','Asset Assignment','Asset Assigned by ".$assby." to ".$assto."','".$price."','".$date."','".$stamp."',1,'".$username."')");
echo"<img src=\"images/tick.png\" style=\"\"  width=\"30\" height=\"30\"/>";
echo"<script>setTimeout(function() {assignasset(79);},500);</script>";	
}
							else {echo"<img src=\"images/delete.png\" style=\"\"  width=\"30\" height=\"30\"/>";}
							
							break;
							
							case 119:
							$regn=$_GET['regn'];
							$assid=$_GET['assid'];
							$dor=$_GET['dor'];
							$recby=$_GET['recby'];
							$odetail=$_GET['odetail'];
							$date=date('Y/m/d');
							$stamp=date('Ymd');
							$price=$_GET['price'];
							$name=$_GET['name'];
		
		$resulta= mysqli_query($db,"update assignments set dor='".$dor."',receivedby='".$recby."',odetail='".$odetail."',status=2 where id=".$assid."");
		$resultb= mysqli_query($db,"update assets set status=1 where assetid='".$regn."'");
			
			if($resulta){
	$resulta = mysqli_query($db,"insert into log values('','".$username." returns asset.Asset No:".$regn."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
	$resultx = mysqli_query($db,"insert into assettrack values('','".$regn."','".$name."','Asset Return','Asset returned  to ".$recby."','".$price."','".$date."','".$stamp."',1,'".$username."')");
echo"<img src=\"images/tick.png\" style=\"\"  width=\"30\" height=\"30\"/>";
echo"<script>setTimeout(function() {returnasset(81);},500);</script>";	
}
							else {echo"<img src=\"images/delete.png\" style=\"\"  width=\"30\" height=\"30\"/>";}
							
							break;
							
							case 120:
					
							$assid=$_GET['param'];
							$result = mysqli_query($db,"select * from assets where id='".$assid."'");
							$row=mysqli_fetch_array($result);
							$regn=stripslashes($row['assetid']);
							$price=stripslashes($row['price']);
							$name=stripslashes($row['name']);
							
							
							$date=date('Y/m/d');
							$stamp=date('Ymd');
						
		
		$resulta= mysqli_query($db,"update assignments set status=0 where assid=".$regn."");
		$resultb= mysqli_query($db,"update assets set status=0 where id='".$assid."'");
			
			if($resulta){
	$resulta = mysqli_query($db,"insert into log values('','".$username." disposes asset.Asset No:".$regn."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
	$resultx = mysqli_query($db,"insert into assettrack values('','".$regn."','".$name."','Asset Disposal','Asset disposed  by ".$username."','".$price."','".$date."','".$stamp."',1,'".$username."')");
	echo"<script>$().customAlert();alert('Success!', '<p>Asset Disposed.</p>');e.preventDefault();</script>";
echo"<script>setTimeout(function() {dispasset(83);},500);</script>";	
}
							else {echo"<script>$().customAlert();alert('Error!', '<p>Failed. Please try again.</p>');e.preventDefault();</script>";}
							
							break;
							
							case 121:
					
							$assid=$_GET['assid'];
							$odetail=$_GET['odetail'];
							$price=$_GET['price'];
							$result = mysqli_query($db,"select * from assets where id='".$assid."'");
							$row=mysqli_fetch_array($result);
							$regn=stripslashes($row['assetid']);
							$name=stripslashes($row['name']);
							
							
							$date=date('Y/m/d');
							$stamp=date('Ymd');
							
							$resultx = mysqli_query($db,"insert into assettrack values('','".$regn."','".$name."','Asset Management','".$odetail."','".$price."','".$date."','".$stamp."',1,'".$username."')");
						
		if($resultx){
	$resulta = mysqli_query($db,"insert into log values('','".$username." manages asset.Asset No:".$regn."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
	
	echo"<script>$().customAlert();alert('Success!', '<p>Details captured.</p>');e.preventDefault();</script>";
echo"<script>setTimeout(function() {dispasset(83);},500);</script>";	
}
							else {echo"<script>$().customAlert();alert('Success!', '<p>Asset Disposed.</p>');e.preventDefault();</script>";}
							
							break;
							
								case 122:
							$code=$_GET['code'];
							$pro=array(array());
							$itname='';
							
								$result =mysqli_query($db,"select * from creditsuppliers order by CustomerName");
								$num_results = mysqli_num_rows($result);
								for ($i=0; $i <$num_results; $i++) {
								$row=mysqli_fetch_array($result);
								$supid=stripslashes($row['CustomerId']);
								$sname=stripslashes($row['CustomerName']);
									
								$resultd =mysqli_query($db,"SELECT * FROM purchases WHERE SupplierId='".$supid."' and ItemCode='".$code."' order by TransNo desc limit 0,1");
								$num_resultsd = mysqli_num_rows($resultd);
								
								if($num_resultsd>0){
								$rowd=mysqli_fetch_array($resultd);
								$price=stripslashes($rowd['PurchPrice']);
								$itname=stripslashes($rowd['ItemName']);
								$max=count($pro);
									if($max==0){
									$pro[0]=array($sname,$price);	
									}else{$pro[$max]=array($sname,$price);		}
								}
							
							}
							
						echo"<script>
							$(window).keydown(function(e) {
							if (e.keyCode == '27') {
								hidealert();
							}
							});
							</script>";
							echo'<div id="modalDiv">	</div>
							<div id="alertDiv" style="width:400px; height:200px;background:#fff;opacity:1000">
							<p class="title" style="margin-top:0">PURCHASE PRICE COMPARISON<img src="images/delete.png" style="width:20px; height:20px; float:right; border:1px solid #fff; border-radius:2px; cursor:pointer" onclick="hidealert()"></p>
							<div class="message" style="padding:5px 15px">
							<h5>'.$itname.'</h5>
							<div id="inside" style="height:106px; overflow-y:auto">
									<div id="title">
									<div id="figure1" style="width:250px">Supplier</div>
									<div id="figure1" style="width:50px">Price</div>
									</div>';
									$max=count($pro);
									for ($i=1; $i <$max; $i++) {
									echo'
									<div id="normal">';
									echo"
									<div id=\"figure2\" style=\"width:250px\">".$pro[$i][0]."</div>
									<div id=\"figure2\" style=\"width:51px\">".$pro[$i][1]."</div>
									</div><div class=\"cleaner\"></div>";
								
									}
									
							
						
							
							echo'</div></div>
							
							</div>';
							break;
							case 123:
				$resulta = mysqli_query($db,"update employee set terminationdate='".$_GET['dot']."',terminationreason='".$_GET['reason']."',status=0 where emp='".$_GET['emp']."'");
					if($resulta){
$resulta = mysqli_query($db,"insert into log values('','".$username." terminates employment .PF No:".$_GET['emp']."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
				echo"<script>$().customAlert();alert('Success!', '<p>Employment terminated.</p>');e.preventDefault();</script>";
									exit;
									}
									else{echo"<script>$().customAlert();alert('Failure!', '<p>Please repeat again.</p>');e.preventDefault();</script>";}
				
				break;
				
				case 124:
				$rdate=$_GET['tdate'];
				$stamp=preg_replace('~/~', '', $rdate);
				$action=$_GET['action'];
				$aid=$_GET['a'];
				
				$x=substr($rdate,8,2);
				$y=substr($rdate,5,2);
				$z=substr($rdate,0,4);
				
				$mon=$y.'_'.$z;
				$k=$x.'c';
				
				$result =mysqli_query($db,"select * from ".$mon." where id='".$aid."'");
				$row=mysqli_fetch_array($result);
				$pfno=stripslashes($row['pfno']);
				
				//check if leave is valid
				
				
				if($action==2){
				
					
				$result =mysqli_query($db,"select * from leaves where endstamp>='".$stamp."' and emp='".$pfno."' and status=2");
				$num_results = mysqli_num_rows($result);	
				if($num_results==0){
					echo"<script>$().customAlert();alert('Error!', '<p>This employee is not on work leave</p>');e.preventDefault();</script>";
			
					$action=0;
				  }
				}
				
					
				if($action==3){
					
				$q=0;
				$result =mysqli_query($db,"select * from ".$mon." where id='".$aid."'");
				$row=mysqli_fetch_array($result);
					
				for ($i=1; $i <32; $i++) {
							$d=sprintf("%02d",$i);
							$d=$d.'c';
							if(stripslashes($row[$d])==3){
								$q++;
							}
				}
				
				if($q>=7){
					echo"<script>$().customAlert();alert('Error!', '<p>Maximum number of days for sick leave exceeded.</p>');e.preventDefault();</script>";
				  	$action=0;	
					}
				}
				
				
				
				
				$resulta = mysqli_query($db,"update  ".$mon." set ".$k."='".$action."' where id='".$_GET['a']."'");
				
				$resultb =mysqli_query($db,"select * from employee where emp='".$pfno."'");
				$rowb=mysqli_fetch_array($resultb);
				$att=stripslashes($rowb['attendance']);
				$tot=stripslashes($rowb['totattendance']);
				
				if($action==0){
					$tot=$tot+1;
					
				}else{
					$tot=$tot+1;
					$att=$att+1;
				}
				
				
				$resultn = mysqli_query($db,"update employee set attendance='".$att."',totattendance='".$tot."' where emp='".$pfno."'");	
				
				break;
				
				case 125:
				$emp=$_GET['emp'];
				$result =mysqli_query($db,"select * from employee where emp='".$emp."' and status=1");
				$num_results = mysqli_num_rows($result);
				if($num_results==0){
								echo"<script>$().customAlert();alert('Error!', '<p>The PF No. entered is incorrect</p>');e.preventDefault();</script>";
						
					  exit;
				}
				
				
				$row=mysqli_fetch_array($result);
				$name=stripslashes($row['fname']).' '.stripslashes($row['mname']).' '.stripslashes($row['lname']);
				$resulta = mysqli_query($db,"insert into leaves values('','".$_GET['emp']."','".$name."','".stripslashes($row['branch'])."','".stripslashes($row['position'])."','".$_GET['from']."','".$_GET['to']."','".$_GET['days']."','".$_GET['shadow']."','".date('d/m/Y')."','".date('Ymd')."',0,'".$username."','".preg_replace('~/~', '',$_GET['from'])."','".preg_replace('~/~', '',$_GET['to'])."')") or die (mysqli_error());
					
					if($resulta){
$resulta = mysqli_query($db,"insert into log values('','".$username." makes a leave application .PF No:".$_GET['emp']."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
					echo"<script>hidenewstude();
					$().customAlert();alert('Success!', '<p>Leave Application successful.</p>');e.preventDefault();</script>";
						
						exit;
					}
					else{	echo"<script>$().customAlert();alert('Failure!', '<p>Please repeat the request</p>');e.preventDefault();</script>";
						
					}
				
				break;
				case 126:
				
				
				
				$lid=$_GET['a'];
				$result =mysqli_query($db,"select * from leaves where id='".$lid."'");
				$row=mysqli_fetch_array($result);
				$emp=stripslashes($row['emp']);
				$cdate=stripslashes($row['commencedate']);
				$days=stripslashes($row['days']);
				
				$resultb =mysqli_query($db,"select * from employee where emp='".$emp."'");
				$rowb=mysqli_fetch_array($resultb);
				$leave=stripslashes($rowb['leaveac']);
				
				$action=$_GET['action'];
				if($action=='Approve'){
					$stat=2;$x='approved';$leave=$leave-$days;
				}else{$stat=1;$x='denied';}
				
				$result =mysqli_query($db,"select * from users where pfno='".$emp."'");
				$row=mysqli_fetch_array($result);
				$user=stripslashes($row['name']);
				
				$resulta = mysqli_query($db,"update leaves set status='".$stat."' where id='".$_GET['a']."'");
				
				
				$resultm = mysqli_query($db,"update employee set leaveac='".$leave."' where emp='".$emp."'");	
			
					if($resulta){

$resulta = mysqli_query($db,"insert into log values('','".$username." authorizes leave .ID No:".$_GET['a']."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
$resultb = mysqli_query($db,"insert into messages values('','".$user."','System','Your Leave Request Commencing on ".$cdate." has been ".$x."','','".date('d/m/Y-H:i')."','".date('Ymd')."',0)");
				exit;
			}
								
				
				break;
				
				case 127:
									
								
									$mon=$_GET['mon'];
									$query =mysqli_query($db,"select * from salregister where month='".$mon."'");
									$count = mysqli_num_rows($query);
									if($count>0){
									echo"<script>
									$().customAlert();
									alert('Error!', '<p>Payroll for the Month has already been created.</p>');
									e.preventDefault();
									</script>";	
										exit;
									}
								else{
									
								
							
									
									
								$resultb =mysqli_query($db,"select * from employee where status=1");
								$num_results = mysqli_num_rows($resultb);
								for ($i=0; $i <$num_results; $i++) {
								$rowb=mysqli_fetch_array($resultb);
								$emp=stripslashes($rowb['emp']);
								$leave=stripslashes($rowb['leaveac']);
								$names=stripslashes($rowb['fname']).' '.stripslashes($rowb['mname']).' '.stripslashes($rowb['lname']);	
								
								$q=0;
								$resultx =mysqli_query($db,"select * from ".$mon." where pfno='".$emp."'");
								if(!$resultx){
									$q=26;
									}
								$rowx=mysqli_fetch_array($resultx);
					
								for ($x=1; $x<32; $x++) {
											$d=sprintf("%02d",$x);
											$d=$d.'c';
											if(stripslashes($rowx[$d])==1||stripslashes($rowx[$d])==2||stripslashes($rowx[$d])==3){
												$q++;
											}
								}
								
								
								$result =mysqli_query($db,"select * from payroll where status=1 and emp='".$emp."' order by serial desc limit 0,1");
								$row=mysqli_fetch_array($result);
														
$resultc = mysqli_query($db,"insert into payroll values('','".$mon."','".stripslashes($rowb['emp'])."','".$names."','".stripslashes($rowb['dept'])."','".stripslashes($row['sal'])."','".stripslashes($row['allow'])."','','','','','','','','','','',0,'".stripslashes($rowb['bid'])."','".stripslashes($rowb['bname'])."','".stripslashes($rowb['acno'])."','".$q."')");

									if($q>=26){
									$leave=$leave+2;	
		$resultm = mysqli_query($db,"update employee set leaveac='".$leave."' where emp='".$emp."'");	
							}


									}
								if($result){
	$resulta = mysqli_query($db,"insert into log values('','".$username." adds new payroll.Month:".$mon."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
	$result= mysqli_query($db,"insert into salregister values('".$mon."','','','','','','','','','','','','','',1)");						
									echo "<script>
									payroll('".$mon."');
									</script>";
									exit;
									}
									else{
									echo "<script>
									$().customAlert();
									alert('Error!', '<p>Payroll not Created</p>');
									e.preventDefault();
									</script>";
									exit;
										}
										
									}
							break;
							
							case 128:
							$emp=$_GET['emp'];
							$phone=$_GET['phone'];if($phone==''){$phone=0;}
							$health=$_GET['health'];if($health==''){$health=0;}
							$vehicle=$_GET['vehicle'];if($vehicle==''){$vehicle=0;}
							$entertainment=$_GET['entertainment'];if($entertainment==''){$entertainment=0;}
							$house=$_GET['house'];if($house==''){$house=0;}
							$perdiem=$_GET['perdiem'];if($perdiem==''){$perdiem=0;}
							$others=$_GET['others'];if($others==''){$others=0;}
							$date= date('m_Y');  
							
							
		
		$resulta = mysqli_query($db,"update benefits set phone='".$phone."',
		health='".$health."',vehicle='".$vehicle."',house='".$house."',entertainment='".$entertainment."',perdiem='".$perdiem."',others='".$others."',status=1 where pfno='".$emp."'");
		
		if($resulta){
	$resulta = mysqli_query($db,"insert into log values('','".$username." edits Employee Benefits;Emp id:".$emp."','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	

									echo'<img src="images/tick.png"  width="22" height="22"/>';
									
									}
								else{
									echo'<img src="images/delete.png"  width="21.5" height="21.5"/>';
									}
								break;
								
				case '129':
							$ser=$_GET['ser'];
							$result = mysqli_query($db,"DELETE from benefits where id='".$ser."'");
							$resulta = mysqli_query($db,"insert into log values('','".$username." deletes data from benefits table','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	

							break;
						
						
						case 130:
									$emp=$_GET['emp'];
									
									$query =mysqli_query($db,"select * from benefits where pfno='".$emp."'");
									$count = mysqli_num_rows($query);
									if($count>0){
										
									
									echo"<script>
									$().customAlert();
									alert('Error!', '<p>Employee already exists in the Benefits scheme.</p>');
									e.preventDefault();
									</script>";
										
									exit;	
									}
									else{
								$resultb =mysqli_query($db,"select * from employee where emp='".$emp."'");
								$rowb=mysqli_fetch_array($resultb);
								$emp=stripslashes($rowb['emp']);
								$names=stripslashes($rowb['fname']).' '.stripslashes($rowb['mname']).' '.stripslashes($rowb['lname']);	
								
					
$resultc = mysqli_query($db,"insert into benefits values('','".$emp."','".$names."','','','','','','','','','1')");				
										
										
									if($resultc){	
	$resulta = mysqli_query($db,"insert into log values('','".$username." inserts new employee into benefits scheme.','".$username."','".date('YmdHi')."','".date('H:i')."','".date('d/m/Y')."','1')");	
								echo'<p><img src="images/tick.png" style="width:30px; height:30px; margin-top:-5px"></p>';
									echo "<script>$('#mon').hide();empben();</script>";
										
									}
									
									}
							break;
							
							case 131:
							$comp=$_GET['comp'];
							$resultf= mysqli_query($db,"update company set CompanyName='".$comp."', License='FULL'");
							echo"<script>alert('Activated!');</script>";
							echo"<script>window.location.href = 'http://code.dev/qubiz/main.php';</script>";
							break;



}

?>
							
							
