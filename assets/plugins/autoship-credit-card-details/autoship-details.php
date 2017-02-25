<br/>
<style>
.autotship td{ text-align:center;}
.autotship td,.autotship th{ border:1px solid #ccc; padding:5px;}
.autotship th{ color:#0074A2; text-transform:uppercase;}
div.pagination {padding: 3px;   margin: 3px;}
div.pagination a {padding: 2px 5px 2px 5px;margin: 2px; border:1px solid #AAAADD; text-decoration: none; /* no underline */
        color: #000099;}
div.pagination a:hover, div.pagination a:active {border: 1px solid #000099;color: #000;}
div.pagination span.current {   padding: 2px 5px 2px 5px; margin:2px;  border: 1px solid #000099; font-weight:bold;background-color: #000099;             color: #FFF;}
div.pagination span.disabled {padding: 2px 5px 2px 5px; margin:2px;border: 1px solid #EEE; color: #DDD;    }
</style>
<h1>Order Details With Credit Cards</h1>

<table class="autotship">
	<tr>
		<th>Sr.No</th>
        <th>Order No.</th>
        <!--<th>Status</th>-->
        <th>Order</th>
        <th>Billing</th>
        <th>Shipping</th>
        <th>Order Total</th>
        <th>Card Details</th>
        <th>Order Date</th>
        <th>Action</th>
	</tr>
    
    <?php
global  $wpdb;

mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or trigger_error("Unable to connect to the database: " . mysql_error()); 
mysql_select_db(DB_NAME) or trigger_error("Unable to switch to the database: " . mysql_error()); 

        /*
                Place code to connect to your DB here.
        */
//      include('config.php');  // include your code to connect to DB. bloodrynane aa

      $tbl_name1= $wpdb->base_prefix."postmeta";
	         

 //your table name
                // How many adjacent pages should be shown on each side?
        $adjacents = 3;

        /*
           First get total number of rows in data table.
           If you have a WHERE clause in your query, make sure you 

mirror it here.
        */
      //$query = "SELECT COUNT(*) as num FROM $tbl_name1 WHERE meta_key='_shipping_method' AND meta_value='free_shipping' ORDER BY post_id DESC";
	  
	  $query = "SELECT COUNT(*) as num FROM $tbl_name1 WHERE meta_key='_payment_method_title' AND meta_value='Credit Card' ORDER BY post_id DESC";
        $total_pages = mysql_fetch_array(mysql_query($query));
        $total_pages = $total_pages['num'];

        /* Setup vars for query. */

        $targetpage = "admin.php?page=autoship-credit-details";         //your file name  (the name of this file)
        $limit = 10;                                                    

         //how many items to show per page
        $page1 = $_GET['pages'];
        if($page1)
                $start = ($page1 - 1) * $limit;                        

 //first item to display on this page
        else
                $start = 0;                                            

                 //if no page var is given, set start to 0

        /* Get data. */
      //$sql = "SELECT * FROM $tbl_name1 WHERE meta_key='_shipping_method' AND meta_value='free_shipping' ORDER BY post_id DESC LIMIT $start, $limit";
	  
	  $sql = "SELECT * FROM $tbl_name1 WHERE meta_key='_payment_method_title' AND meta_value='Credit Card' ORDER BY post_id DESC LIMIT $start, $limit";
        $result = mysql_query($sql);

        /* Setup page vars for display. */
        if ($page1 == 0) $page1 = 1;                                   

 //if no page var is given, default to 1.
        $prev = $page1 - 1;                                            

         //previous page is page - 1
        $next = $page1 + 1;                                            

         //next page is page + 1
        $lastpage = ceil($total_pages/$limit);          //lastpage is = total pages /items per page, rounded up.
        $lpm1 = $lastpage - 1;                                         

 //last page minus 1

        /*
                Now we apply our rules and draw the pagination object.
                We're actually saving the code to a variable in case 

we want to draw
it more than once.
        */
        $pagination1 = "";
        if($lastpage > 1)
        {
                $pagination1 .= "<div class=\"pagination\">";
                //previous button
                if ($page1 > 1)
                        $pagination1.= "<a href=\"$targetpage&pages=

$prev\">previous</a>";
                else
                        $pagination1.= "<span class=\"disabled

\">previous</span>";

                //pages
                if ($lastpage < 7 + ($adjacents * 2))   //not enough pages to bother breaking it up
                {
                        for ($counter = 1; $counter <= $lastpage; 

$counter++)
                        {
                                if ($counter == $page1)
                                        $pagination1.= "<span class=

\"current\">$counter</span>";
                                else
                                        $pagination1.= "<a href=

\"$targetpage&pages=$counter\">$counter</a>";
                        }
                }
                elseif($lastpage > 5 + ($adjacents * 2))        

//enough pages to hide some
                {
                        //close to beginning; only hide later pages
                        if($page1 < 1 + ($adjacents * 2))
                        {
                                for ($counter = 1; $counter < 4 + 

($adjacents * 2); $counter++)
                                {
                                        if ($counter == $page1)
                                                $pagination1.= "<span 

class=\"current\">$counter</span>";
                                        else
                                                $pagination1.= "<a 

href=\"$targetpage&pages=$counter\">$counter</a>";
                                }
                                $pagination1.= "...";
                                $pagination1.= "<a href=

\"$targetpage&pages=$lpm1\">$lpm1</a>";
                                $pagination1.= "<a href=

\"$targetpage&pages=$lastpage\">$lastpage</a>";
                        }
                        //in middle; hide some front and some back
                        elseif($lastpage - ($adjacents * 2) > $page1 

&& $page1 > ($adjacents * 2))
                        {
                                $pagination1.= "<a href=

\"$targetpage&pages=1\">1</a>";
                                $pagination1.= "<a href=

\"$targetpage&pages=2\">2</a>";
                                $pagination1.= "...";
                                for ($counter = $page1 - $adjacents; 

$counter <= $page1 +
$adjacents; $counter++)
                                {
                                        if ($counter == $page1)
                                                $pagination1.= "<span 

class=\"current\">$counter</span>";
                                        else
                                                $pagination1.= "<a 

href=\"$targetpage&pages=$counter\">$counter</a>";
                                }
                                $pagination1.= "...";
                                $pagination1.= "<a href=

\"$targetpage&pages=$lpm1\">$lpm1</a>";
                                $pagination1.= "<a href=

\"$targetpage&pages=$lastpage\">$lastpage</a>";
                        }
                        //close to end; only hide early pages
                        else
                        {
                                $pagination1.= "<a href=

\"$targetpage&pages=1\">1</a>";
                                $pagination1.= "<a href=

\"$targetpage&pages=2\">2</a>";
                                $pagination1.= "...";
                                for ($counter = $lastpage - (2 + 

($adjacents * 2)); $counter <=
$lastpage; $counter++)
                                {
                                        if ($counter == $page1)
                                                $pagination1.= "<span 

class=\"current\">$counter</span>";
                                        else
                                                $pagination1.= "<a 

href=\"$targetpage&pages=$counter\">$counter</a>";
                                }
                        }
                }

                //next button
                if ($page1 < $counter - 1)
                        $pagination1.= "<a href=\"$targetpage&pages=

$next\">next</a>";
                else
                        $pagination1.= "<span class=\"disabled

\">next</span>";
                $pagination1.= "</div>\n";
        }
?>


<?php 
$m = 1;
while($row = mysql_fetch_array($result)){
	$post_ids = $row['post_id'];
	$custom_fields = get_post_custom($post_ids);
?>    
    <tr>
		<td><?php echo $m;?></td>
        <td>#<?php echo $row['post_id'];?></td>
        
       <?php /*?> <td>
		<?php 
		
		$sql3 = mysql_query("SELECT * FROM ".$wpdb->base_prefix."term_relationships a, ".$wpdb->base_prefix."terms b WHERE a.object_id='".$post_ids."' AND a.term_taxonomy_id=b.term_id") or die(mysql_error());
		$row3 = mysql_fetch_array($sql3);
		echo $row3['name'];		
		?>
      
        
        </td><?php */?>
        
        <td><?php  
$my_custom_field = $custom_fields['_billing_first_name'];
foreach ( $my_custom_field as $key => $bfistname ) ?>
<?php echo $bfistname; ?>

<?php  
$my_custom_field = $custom_fields['_billing_last_name'];
foreach ( $my_custom_field as $key => $blastname ) ?>
<?php echo $blastname; ?><br/>

Email: <?php  
$my_custom_field = $custom_fields['_billing_email'];
foreach ( $my_custom_field as $key => $bemail ) ?>
<a href="mailto:<?php echo $bemail; ?>"><?php echo " ".$bemail; ?></a><br/>

Tel: <?php  
$my_custom_field = $custom_fields['_billing_phone'];
foreach ( $my_custom_field as $key => $bphone ) ?>
<a href="Tel:<?php echo $bphone; ?>"><?php echo " ".$bphone; ?></a>
</td>
        <td>
		<?php echo $bfistname; ?> <?php echo $blastname; ?><br/>
        <?php  
$my_custom_field = $custom_fields['_billing_company'];
foreach ( $my_custom_field as $key => $bcompany ) ?>
<?php echo $bcompany; ?>

<?php  
$my_custom_field = $custom_fields['_billing_address_1'];
foreach ( $my_custom_field as $key => $baddress_1) ?>
<?php echo $baddress_1; ?>

<?php  
$my_custom_field = $custom_fields['_billing_address_2'];
foreach ( $my_custom_field as $key => $baddress_2) ?>
<?php echo $baddress_2; ?><br/>

<?php  
$my_custom_field = $custom_fields['_billing_city'];
foreach ( $my_custom_field as $key => $bcity) ?>
<?php echo $bcity; ?>

<?php  
$my_custom_field = $custom_fields['_billing_state'];
foreach ( $my_custom_field as $key => $bstate) ?>
<?php echo $bstate; ?>

<?php  
$my_custom_field = $custom_fields['_billing_postcode'];
foreach ( $my_custom_field as $key => $bpostcode) ?>
<?php echo $bpostcode; ?><br/>

<?php  
$my_custom_field = $custom_fields['_payment_method_title'];
foreach ( $my_custom_field as $key => $payment_method_title) ?>
<?php echo "<div style='color: #999999;display: block;font-size: inherit;margin: 3px 0;'>via ".$payment_method_title."</div>"; ?>
        </td>
        
        
        <td>
        <?php  
$my_custom_field = $custom_fields['_shipping_first_name'];
foreach ( $my_custom_field as $key => $sfistname ) ?>

<?php  
$my_custom_field = $custom_fields['_shipping_last_name'];
foreach ( $my_custom_field as $key => $slastname ) ?>
		<?php echo $sfistname; ?> <?php echo $slastname; ?><br/>
        <?php  
$my_custom_field = $custom_fields['_shipping_company'];
foreach ( $my_custom_field as $key => $scompany ) ?>
<?php echo $scompany; ?>

<?php  
$my_custom_field = $custom_fields['_shipping_address_1'];
foreach ( $my_custom_field as $key => $saddress_1) ?>
<?php echo $saddress_1; ?>

<?php  
$my_custom_field = $custom_fields['_shipping_address_2'];
foreach ( $my_custom_field as $key => $saddress_2) ?>
<?php echo $saddress_2; ?><br/>

<?php  
$my_custom_field = $custom_fields['_shipping_city'];
foreach ( $my_custom_field as $key => $scity) ?>
<?php echo $scity; ?>

<?php  
$my_custom_field = $custom_fields['_shipping_state'];
foreach ( $my_custom_field as $key => $sstate) ?>
<?php echo $sstate; ?>

<?php  
$my_custom_field = $custom_fields['_shipping_postcode'];
foreach ( $my_custom_field as $key => $spostcode) ?>
<?php echo $spostcode; ?><br/>

<?php /*?><?php  
$my_custom_field = $custom_fields['_shipping_method_title'];
foreach ( $my_custom_field as $key => $shipping_method_title) ?>
<?php echo "<div style='color: #999999;display: block;font-size: inherit;margin: 3px 0;'>via ".$shipping_method_title."</div>"; ?><?php */?>
        </td>        
        <td><?php  
$my_custom_field = $custom_fields['_order_total'];
foreach ( $my_custom_field as $key => $order_total) ?>
<?php echo "$".$order_total; ?></td>
        
        <td>
        <strong style='color:#0074A2;'>Card No:</strong> <br/><?php if($custom_fields['_billing_creditcard']!=""){ 
$my_custom_field = $custom_fields['_billing_creditcard'];
foreach ( $my_custom_field as $key => $billing_credit_cards1) ?>
<?php echo " ".$billing_credit_cards1.""; } ?><br>

<strong style='color:#0074A2;'>Expiry Month:</strong><br/>

<?php 
if($custom_fields['_billing_expirymm']!=""){  
$my_custom_field = $custom_fields['_billing_expirymm'];
foreach ( $my_custom_field as $key => $billing_expiry_mms1) ?>
<?php echo " ".$billing_expiry_mms1.""; }?></br>

<strong style='color:#0074A2;'>Expiry Year:</strong><br/>
<?php 
if($custom_fields['_billing_expiryyy']!=""){  
$my_custom_field = $custom_fields['_billing_expiryyy'];
foreach ( $my_custom_field as $key => $billing_expiry_yys1) ?>
<?php echo " ".$billing_expiry_yys1.""; }?><br>

<strong style='color:#0074A2;'>CVV:</strong><br/>
<?php  
if($custom_fields['_billing_cvv']!=""){ 
$my_custom_field = $custom_fields['_billing_cvv'];
foreach ( $my_custom_field as $key => $billing_cvvs) ?>
<?php echo " ".$billing_cvvs.""; }?>	
        </td>
        
        <td> <?php $sql1 = mysql_query("SELECT * FROM ".$wpdb->base_prefix."posts WHERE ID='$post_ids'") or die(mysql_error());
		$row1 = mysql_fetch_array($sql1);
		echo $row1['post_title'];
		?> </td>
        
        <td>
        <a target="_blank" href="<?php echo home_url();?>/wp-admin/post.php?post=<?php echo $post_ids;?>&action=edit">View</a>
        </td>
	</tr>
    <?php $m++;}?>
    
    
</table>
<?php echo $pagination1; ?>