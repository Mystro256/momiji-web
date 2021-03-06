<?php

	echo "<pre>";


	require_once('config.php');
	require_once('inc/util.php');
	require_once('inc/mysql.php');
	
	if (!isset($_GET['id'])){
		
		die("Error : specify the artists' ID as a get parameter!");
	}
	$salesArray = findSales($connection, $_GET['id']);
	$sales = array();
	$pns = array();
	foreach ($salesArray as $key => $sale){
		$items_sold_old = explode('#', trim($sale['itemArray'], "#"));
		$items_sold = array_map('strtoupper',$items_sold_old); // silly tiago, caps are for kids ;) <3 meg
		$prices = explode('#', trim($sale['priceArray'], '#'));
		foreach ($items_sold as $item_sold_key => $item_sold){
			
			if(compareItemCodeWithID($item_sold, $_GET['id'])){
				if(isGS($item_sold)){
					$sales[$item_sold] = $prices[$item_sold_key];
					$pns[$item_sold]++;
				} else {
					$sales[$item_sold] = $prices[$item_sold_key];
				}
				
			}
			
		}
		
	}
	
	
?>


<img src="logo.png"><h1>Artist Sales Summary</h1>

<h2>Auction/Quick Sales :</h2>
<table border=1><tr>
<?php
	$total = 0;
	$td = 0;
	foreach ($sales as $key => $sale){
		if (!isGS($key)){
			echo "<td>" . $key . " - <b>$" . number_format($sale,2) . "</b></td>";
			$td++;
			$total +=$sale;
			if ($td % 6 == 0){ echo "</tr><tr>";}
		}
	}
	
	
	
	$total_after_commission = $total * (1-((INT)COMMISSION_AS / 100));
	$final_balance += $total_after_commission;
	echo "</tr></table>";
	echo "Total made : <b>$" . number_format($total,2) . "</b><br>"; 
	echo "Commission taken (<b>".COMMISSION_AS."%</b>): <b>$" . number_format($total - $total_after_commission,2) . "</b><br><hr>";
	echo "Final Balance : <b>$" . number_format($total_after_commission,2) . "<br>";

?>

<h2>Gallery Store Sales :</h2>
<table border=1><tr>
<?php
	$total = 0;
	$td = 0;
	foreach ($sales as $key => $sale){
		if (isGS($key)){
			echo "<td>" . $key . "<sup>x".$pns[$key]."</sup> - <b>$" . number_format($sale,2) . "</b></td>";
			$td++;

			$total +=$pns[$key]*$sale;
			if ($td % 6 == 0){ echo "</tr><tr>";}
		}
	}
	
	
	
	$total_after_commission = $total * (1-((INT)COMMISSION_GS / 100));
	$final_balance += $total_after_commission;
	echo "</tr></table>";
	echo "Total made : <b>$" . number_format($total,2) . "</b><br>"; 
	echo "Commission taken (<b>".COMMISSION_GS."%</b>): <b>$" . number_format($total - $total_after_commission,2) . "</b><br><hr>";
	echo "Final Balance : <b>$" . number_format($total_after_commission,2) . "<br>";
	echo "<hr><br><br><br>";
	echo "Total of Auction/Gallery store balances : <b>$" . number_format(	$final_balance,2) . "</b>";

?>