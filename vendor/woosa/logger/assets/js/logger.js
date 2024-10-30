( function($, woosa){

   if ( ! woosa ) {
      return;
   }

   var Ajax = woosa.ajax;
   var Translation = woosa.translation;

   var ProcessLogger = {

      init: function(){

         //prevent the window which says "the changes may be lost"
         $(document).on('load click change', function(){
            window.onbeforeunload = null;
         });

         this.show_notification();

         this.close_notification();

         this.enable_log_actions();

         this.perform_log_action();
      },


      /**
       * Show notification
       */
      show_notification: function(){

         setTimeout(function(){
            $.ajax({
               url: Ajax.url,
               method: 'POST',
               data: {
                  action: woosa.prefix+'_log_notification',
                  security: Ajax.nonce,
               },
               success: function(res){

                  if(res.success){
                     $('body').append(res.data.html);
                     $('.'+woosa.prefix+'-log-notification').each(function(index, item){
                        var offset = 110,
                           bottom = index == 0 ? 10 : offset * index;

                        $(this).animate({
                           'bottom': bottom
                        }).slideDown('fast');
                     });
                  }
               }
            });
         }, 800);

      },


      /**
       * Close notification box
       */
      close_notification: function(){

         $(document).on('click', '.'+woosa.prefix+'-log-notification__close', function(){
            $(this).closest('.'+woosa.prefix+'-log-notification').hide();

            //refresh bottom position
            $('.'+woosa.prefix+'-log-notification:visible').each(function(index, item){
               var offset = 110,
                  bottom = index == 0 ? 10 : offset * index;

               $(this).animate({
                  'bottom': bottom
               }).slideDown('fast');
            });
         });

      },


      /**
       * Enable the action buttons when at least a checkbox field is checked
       */
      enable_log_actions: function(){

         $(document).on('change', '[data-'+woosa.prefix+'-log-checkbox]', function(){

            var _this = $(this),
               log_list = _this.closest('.'+woosa.prefix+'-logs');

            if(log_list.find('input:checkbox:checked').length > 0){
               log_list.find('.'+woosa.prefix+'-log-actions__right button').prop('disabled', false);
            }else{
               log_list.find('.'+woosa.prefix+'-log-actions__right button').prop('disabled', true);
            }

         });
      },


      /**
       * Performs the selected action.
       */
      perform_log_action: function(){

         $(document).on('click', '[data-'+woosa.prefix+'-log-action]', function(e){

            var _this = $(this),
               log_action = _this.attr('data-'+woosa.prefix+'-log-action'),
               log_elem = _this.closest('[data-'+woosa.prefix+'-log-code]'),
               log_list = _this.closest('.'+woosa.prefix+'-logs'),
               log_code = log_elem.attr('data-'+woosa.prefix+'-log-code');

            if('view_detail' === log_action){

               var url = Ajax.url+'?action='+woosa.prefix+'_log_action&security='+Ajax.nonce+'&log_action='+log_action+'&log_code='+log_code+'&width=700&height=700';

               tb_show('Log detail', url);

            }else if('select_all' === log_action){

               var checked = _this.data('checked');

               if(checked == true){

                  _this.data('checked', false);
                  checked = false;
                  _this.removeClass('button-primary');

                  log_list.find('.'+woosa.prefix+'-log-actions__right button').prop('disabled', true);

               }else{

                  _this.data('checked', true);
                  checked = true;
                  _this.addClass('button-primary');

                  log_list.find('.'+woosa.prefix+'-log-actions__right button').prop('disabled', false);
               }

               log_list.find('input:checkbox').prop('checked', checked);

            }else{

               var log_codes = [];

               log_list.find('input:checkbox:checked').each(function(){
                  log_codes.push($(this).val());
               });

               if('remove' === log_action){
                  if( ! window.confirm('Are you sure you want to remove this log?') ) {
                     return;
                  }
               }

               $.ajax({
                  url: Ajax.url,
                  method: 'POST',
                  data: {
                     action: woosa.prefix+'_log_action',
                     security: Ajax.nonce,
                     log_action: log_action,
                     log_codes: log_codes,
                  },
                  beforeSend: function(){

                     _this.attr('disabled', true);

                     jQuery('#wpcontent').block({
                        message: null,
                        overlayCSS: {
                           background: '#fff',
                           opacity: 0.6
                        }
                     });

                  },
                  success: function(res){

                     if(res.success){

                        window.location.reload();

                     }else{

                        console.error(res.data.message);

                        _this.attr('disabled', false);

                        jQuery('#wpcontent').unblock();
                     }
                  },
               });
            }

         });

      }

   };

   $( document ).ready( function() {
      ProcessLogger.init();
   });


})( jQuery, srft_logger );