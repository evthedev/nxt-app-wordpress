jQuery(document).ready(function($){
  $('.NXTBridge-tip-button').click(function(e) {
    e.preventDefault(); 
    var addr = $(this).data('addr');
    var uid = $(this).data('id');
    var amount = $('#Tip-' + uid).val();

    // save into cookie
    var exdate = new Date();
    exdate.setDate( exdate.getDate() + 14);
    document.cookie = 'NXTBridgeTip_addr' + '=' + escape(addr) + ';expires=' + exdate.toUTCString() + ';path=/';
    document.cookie = 'NXTBridgeTip_amount' + '=' + escape(amount) + ';expires=' + exdate.toUTCString() + ';path=/';

    // redirect to wallet page
    document.location.href='/wp-admin/admin.php?page=nxtbridge_wallet&sub=send';
  });


});
