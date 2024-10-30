( function($, woosa){

   if ( ! woosa ) {
      return;
   }

   var Ajax = woosa.ajax;
   var Translation = woosa.translation;
   var Prefix = woosa.prefix;

   var ProcessOrder = {

      init: function(){

         //prevent the window which says "the changes may be lost"
         $(document).on('load click change', function(){
            window.onbeforeunload = null;
         });

         this.toggle_affiliate();
      },

      toggle_affiliate: function(){

         $(document).on('click', '[data-'+Prefix+'-order-affiliate] button[type="button"]', function(){

            if( window.confirm('Are you sure you want to do this?') ) {

               var button = $(this),
                  action = button.attr('data-action'),
                  btn_parent = button.parent(),
                  wrapper = button.closest('[data-'+Prefix+'-order-affiliate]'),
                  input = wrapper.find('select, textarea, input, button');

               $.ajax({
                  url: Ajax.url,
                  method: "POST",
                  data: {
                     action: Prefix+'_'+action+'_order_affiliate',
                     security: Ajax.nonce,
                     fields: input.serialize(),
                  },
                  beforeSend: function(){

                     input.prop('disabled', true);

                     button.data('label', button.text()).text(Translation.processing);

                     jQuery('#wpcontent').block({
                        message: null,
                        overlayCSS: {
                           background: '#fff',
                           opacity: 0.6
                        }
                     });

                     wrapper.find('.ajax-response').remove();
                  },
                  success: function(res) {

                     $('<p class="ajax-response">'+res.message+'</p>').insertAfter(btn_parent);

                     if(res.success){
                        setTimeout(function(){
                           window.location.reload();
                        }, 1200);
                     }

                  },
                  complete: function(){

                     button.text( button.data('label') );

                     input.prop('disabled', false);

                     jQuery('#wpcontent').unblock();
                  }
               });
            }
         });

      },
   };

   $( document ).ready( function() {
      ProcessOrder.init();
   });


})( jQuery, srft_order );