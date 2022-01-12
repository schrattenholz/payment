/*vendor\schrattenholz\delivery\javascript\delivery.js*/

function loadPaymentMethods(deliveryTypeID,paymentMethodID){
	jQuery.ajax({
		url: pageLink+"/getPaymentMethods?deliveryTypeID="+deliveryTypeID+"&paymentMethodID="+paymentMethodID,
		success: function(data) {
		
		/*
		JSON
			$returnValues->Status=false;
			$returnValues->Message="Das Passwort muss mindestens 8 Zeiechen haben!";
			$returnValues->Value='object';
		*/
					$('#paymenMethods_Holder').html(data);

		}
	});
}
function setPaymentMethodID(id){$('#PaymentMethodID').val(id);}
