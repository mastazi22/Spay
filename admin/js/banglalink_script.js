$(document).ready(function(){
    $(".topup_now").live("click",function(event){
       var row= $(this).parent().parent();
       var values=new rowParser(row,'topup_now');
       if(confirm("Sure want to topup this Mobile?\n\n"+"Mobile:\t\t"+values.mobile+"\nAmount:\t\t"+values.amount+" \nTc TXID:\t\t"+values.tc_txid)== false){
           return false;
       }
       $(this).addClass('ajax-loader');
       $.ajax({
               type: "POST",
               url: "ajax_request.php",
               data: values.returnOBJ,
               dataType:"json",
               success: function(rdata){
                if(rdata.Status=='300'){
                    $(row).fadeOut('slow');
                }
                else{
                	$(row).find('.topup_now').removeClass("ajax-loader").addClass('failed_info');
                }
               },
               statusCode: {
                404: function() {alert('page not found');}
               }
             });// end ajax call
             
       });// end topup now click
       
       // --------------
      $(".mark_refund").live("click",function(event){
       var row= $(this).parent().parent();
       var values=new rowParser(row,'mark_refund');
       $(this).addClass('ajax-loader');
       $.ajax({
               type: "POST",
               url: "ajax_request.php",
               data: values.returnOBJ,
               dataType:"json",
               success: function(rdata){
	            if(rdata.status=='FAILED'){
	                $(row).find('.mark_refund').removeClass("ajax-loader").addClass('failed_info');
	            }
	            else if(rdata.status=='success'){
	                $(row).find('.mark_refund').removeClass("ajax-loader").addClass('marked_refund');
	                $(row).find('.topup_now').removeClass("topup_now").addClass('disabled')
	            }
               },
               statusCode: {
                	404: function() {alert('page not found');}
               }
             });// end ajax call
             
       });// end topup now click
       //----------------
       
       // --------------
      $(".show_details").live("click",function(event){
       var row= $(this).parent().parent();
       var values=new rowParser(row,'show_details');
       $(this).addClass('ajax-loader');
       $.ajax({
               type: "POST",
               url: "ajax_request.php",
               data: values.returnOBJ,
               dataType:"html",
               success: function(data_html){
                if(data_html != ""){
                    $('.details_view_mask').fadeIn('slow',function(){
                        $('.details_view').html(data_html);
                        $(row).find('.ajax-loader').removeClass('ajax-loader');
                    });
                }
               },
               statusCode: {
                404: function() {alert('page not found');}
               }
             });// end ajax call
             
       });// end topup now click
       //----------------
      
      $('.details_view_mask').live("click",function(){
          $(this).fadeOut('fast');
          $('.details_view').html('');
      });
      $('.details_view').live("click",function(event){
          return false;
      });

    
});
// end $(document).ready()

function rowParser($row,$action_type){
    this.htmlObj=$row;
    this.action=$action_type;
    this.mobile=$(this.htmlObj).find($('.tc_mobile')).html();
    this.epay_txid=$(this.htmlObj).find($('.epay_txid')).html();
    this.tc_txid=$(this.htmlObj).find($('.tc_txid')).html();
    this.amount=$(this.htmlObj).find($('.tc_amount')).html();
    this.returnOBJ={mobile:this.mobile, epay_txid:this.epay_txid, tc_txid:this.tc_txid, amount:this.amount,action:this.action};
}