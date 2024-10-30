( function($, woosa){

   if ( ! woosa ) {
      return;
   }

   var Ajax = woosa.ajax;
   var Translation = woosa.translation;
   var Prefix = woosa.prefix;

   var ProcessSettings = {

      init: function(){

         this.view_search_results();

         $('[data-'+Prefix+'-select2="yes"]').select2();
      },

      view_search_results: function(){

         $(document).on('click', '[data-'+Prefix+'-view-search-results]', function(){

            var _this  = $(this),
               filters = _this.closest('[data-'+Prefix+'-search-filters]'),
               results = $('[data-'+Prefix+'-search-results]'),
               fields  = filters.find('select, textarea, input').serialize();

            $.ajax({
               url: Ajax.url,
               method: "POST",
               data: {
                  action: Prefix + '_render_search_results',
                  security: Ajax.nonce,
                  fields: fields,
               },
               beforeSend: function(){

                  $('#wpcontent').block({
                     message: null,
                     overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                     }
                  });

                  _this.next('.ajax-response').remove();

                  _this.closest('#mainform').find(':input').prop('disabled', true);

               },
               success: function(res) {

                  if(res.success){

                     results.html(res.data.html);

                  }else{

                     results.htm('<p class="ajax-response error">'+res.data.message+'</p>');

                     _this.text( _this.data('label') );
                  }
               },
               complete: function(){

                  $('#wpcontent').unblock();

                  _this.closest('#mainform').find(':input').not('.always_disabled').prop('disabled', false);
               }
            });
         });

      },
   };

   $( document ).ready( function() {
      ProcessSettings.init();
   });


})( jQuery, srft_module_core );