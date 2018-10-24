<?php
// Merchant key here as provided by Payu
$MERCHANT_KEY = "merchant key";

// Merchant Salt as provided by Payu
$SALT = "salt key";

// End point - change to https://secure.payu.in for LIVE mode
$PAYU_BASE_URL = "https://secure.payu.in";

$action = '';

$posted = array();
if(!empty($_POST)) {
    //print_r($_POST);
  foreach($_POST as $key => $value) {    
    $posted[$key] = $value; 
	
  }
}

$formError = 0;

if(empty($posted['txnid'])) {
  // Generate random transaction id
  $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
} else {
  $txnid = $posted['txnid'];
}
$hash = '';
// Hash Sequence
$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
if(empty($posted['hash']) && sizeof($posted) > 0) {
  if(
          empty($posted['key'])
          || empty($posted['txnid'])
          || empty($posted['amount'])
          || empty($posted['firstname'])
          || empty($posted['email'])
          || empty($posted['phone'])
          || empty($posted['productinfo'])
          || empty($posted['surl'])
          || empty($posted['furl'])
		  || empty($posted['service_provider'])
  ) {
    $formError = 1;
  } else {
    //$posted['productinfo'] = json_encode(json_decode('[{"name":"tutionfee","description":"","value":"500","isRequired":"false"},{"name":"developmentfee","description":"monthly tution fee","value":"1500","isRequired":"false"}]'));
	$hashVarsSeq = explode('|', $hashSequence);
    $hash_string = '';	
	foreach($hashVarsSeq as $hash_var) {
      $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
      $hash_string .= '|';
    }

    $hash_string .= $SALT;


    $hash = strtolower(hash('sha512', $hash_string));
    $action = $PAYU_BASE_URL . '/_payment';
  }
} elseif(!empty($posted['hash'])) {
  $hash = $posted['hash'];
  $action = $PAYU_BASE_URL . '/_payment';
}
?>
<html>
  <head>
  <script>
    var hash = '<?php echo $hash ?>';
    function submitPayuForm() {
      if(hash == '') {
        return;
      }
      var payuForm = document.forms.payuForm;
      payuForm.submit();
    }
  </script>
  <head>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/timepicker@1.11.12/jquery.timepicker.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/timepicker@1.11.12/jquery.timepicker.js"></script>
    <script>
        $( function() {
            $( "#datepicker" ).datepicker();
        } );
    </script>
    <script>
        $(function() {
            var scntDiv = $('#p_scents');
            var i = $('#p_scents p').size() + 1;

            $('#addScnt').live('click', function() {
                $('<p><label for="p_scnts"><input type="text" id="p_scnt" size="20" name="p_scnt_' + i +'" value="" placeholder="Input Value" /></label> <a href="#" id="remScnt">Remove</a></p>').appendTo(scntDiv);
                i++;
                return false;
            });

            $('#remScnt').live('click', function() {
                if( i > 2 ) {
                    $(this).parents('p').remove();
                    i--;
                }
                return false;
            });
        });

    </script>
  <body onload="submitPayuForm()">
    <h2><center>PayU Form</center></h2>
    <br/>
    <?php if($formError) { ?>
	
      <span style="color:red">Please fill all mandatory fields.</span>
      <br/>
      <br/>
    <?php } ?>

    <div class="container">
            <div class="row border-class">


<!--        <div class="main">-->
        <div class="section-lft col-md-6 col-sm-12 col-12">
        <p>
            STEP 1 : CONTACT INFORMATION
        </p>


    <form action="<?php echo $action; ?>" method="post" name="payuForm">
      <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
      <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
      <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
     
        
        <div  class="form-input">
          <label>Amount: </label>
          <input name="amount" value="<?php echo (empty($posted['amount'])) ? '' : $posted['amount'] ?>" />
          <!-- <p>First Name: </p>
          <p><input name="firstname" id="firstname" value="<?php echo (empty($posted['firstname'])) ? '' : $posted['firstname']; ?>" /></p> -->
        </div>


        <div class="form-input">
                <label>First Name:*</label>
                <input required name="firstname" placeholder="Enter your first name" value="<?php echo (empty($posted['firstname'])) ? '' : $posted['firstname'] ?>" />
        </div>


        <div class="form-input">
          <label>Email: </label>
          <input name="email" id="email" value="<?php echo (empty($posted['email'])) ? '' : $posted['email']; ?>" />
        </div>

        <div class="form-input">
          <label>Phone:  </label>
         <input name="phone" value="<?php echo (empty($posted['phone'])) ? '' : $posted['phone']; ?>" /> 
        </div>

        <div style="display: none;">
          <p>Product Info: </p>
          <p colspan="3"><textarea name="productinfo"><?php echo (empty($posted['productinfo'])) ? 'productinfo' : $posted['productinfo'] ?></textarea></p>
        </div>


        <div>
            <p>STEP 2 : APPOINTMENT DETAILS</p>

            <div class="form-input">
                <label for="">Date: </label>
            <input required name="surl" placeholder="Choose Date" id="datepicker" />
            </div>

            <div class="form-input">
                <label>Time:</label>
                <input required name="surl" id="timepicker" placeholder="00:00" />
            </div>
            
            <div class="form-input">
                <label>Location:</label>
                <select>
                    <option value="Colaba">Colaba</option>
                    <option value="NepeanSea">NepeanSea</option>
                    <option value="BreachSea">BreachSea</option>
                    <option value="Bandra">Bandra</option>
                    <option value="Khar">Khar</option>
                    <option value="Versova">Versova</option>
                    <option value="Lokhandwala">Lokhandwala</option>
                    <option value="Powai">Powai</option>
                </select>
            </div>

        </div>

        <div style="display: none;">
          <p>Success URI: </p>
          <p colspan="3"><input name="surl" value="http://localhost/Payment_Gateway/success.php" size="64" /></p>
        </div>
        <div style="display: none;">
          <p>Failure URI: </p>
          <p colspan="3"><input name="furl" value="http://localhost/Payment_Gateway/failure.php" size="64" /></p>
        </div>

        <div>
          <td colspan="3"><input type="hidden" name="service_provider" value="payu_paisa" size="64" /></td>
        </div>

       
        <!-- <tr>
          <?php if(!$hash) { ?>
            <td colspan="4"><input type="submit" value="Submit" /></td>
          <?php } ?>
        </tr> -->

        <div>
            <?php if(!$hash) { ?>
                <p colspan="4">
                    <input type="submit" class="btn btn-success book-now" value="Book now" />
                    <a class="btn btn-dark" href="http://nailspaexperience.com">Back</a>
                </p>
            <?php } ?>
        </div>
            </form>
          </div>


          
<!---->
<!--        </div>-->
        </div>


        <script>
    $(function() {
        $('#timepicker').timepicker({
            //timeFormat: 'h:mm p',
            minTime: '10',
            maxTime: '9:00pm',
            dynamic: false,
            dropdown: true,
            scrollbar: true
        });
    });
</script>

  </body>
</html>
