/**
 * Created by sebastien on 27/07/2014.
 */

jQuery(document).ready(function ($) {
    // Instantiates the variable that holds the media library frame.
    var meta_image_frame;
    var key=meta_image.key;

    $('#'+key+'-reset').click(function(e) {
        $('#'+key).val("");
        $(this).hide();
        $('#'+key+'-button span').show();
        $('#'+key+'-button img').hide().remove();
    });

    $('#'+key+'-button').click(function(e) {
    /**
     * Please attach all the code below to a button click event
     **/

//create a new Library, base on defaults
//you can put your attributes in
    var insertImage = wp.media.controller.Library.extend({
        defaults :  _.defaults({
            id:        'insert-image',
            title:      meta_image.title,
//            allowLocalEdits: false,
//            displaySettings: false,
//            displayUserSettings: false,
//            multiple : true,
            type : 'image'//audio, video, application/pdf, ... etc
        }, wp.media.controller.Library.prototype.defaults )
    });

//Setup media frame
    var frame = wp.media({
        button : { text : meta_image.button },
        state : 'insert-image',
        states : [
            new insertImage()
        ]
    });

//on close, if there is no select files, remove all the files already selected in your main frame
    frame.on('close',function() {
        var selection = frame.state('insert-image').get('selection');
        if(!selection.length){
//        #remove file nodes
//        #such as: jq("#my_file_group_field").children('div.image_group_row').remove();
//        #...
        }
    });


    frame.on( 'select',function() {
        var state = frame.state('insert-image');
        var selection = state.get('selection');
        var imageArray = [];

        if ( ! selection ){
            $('#'+key+'-reset').trigger('click');
            return;
        }

//    #remove file nodes
//    #such as: jq("#my_file_group_field").children('div.image_group_row').remove();
//    #...

        //to get right side attachment UI info, such as: size and alignments
        //org code from /wp-includes/js/media-editor.js, arround `line 603 -- send: { ... attachment: function( props, attachment ) { ... `
        selection.each(function(attachment) {
            var display = state.display( attachment ).toJSON();
            var obj_attachment = attachment.toJSON()
            var caption = obj_attachment.caption, options, html;

            // If captions are disabled, clear the caption.
            if ( ! wp.media.view.settings.captions )
                delete obj_attachment.caption;

            display = wp.media.string.props( display, obj_attachment );

            options = {
                id:        obj_attachment.id,
                post_content: obj_attachment.description,
                post_excerpt: caption
            };

            if ( display.linkUrl )
                options.url = display.linkUrl;

            if ( 'image' === obj_attachment.type ) {
                html = wp.media.string.image( display );
                _.each({
                    align: 'align',
                    size:  'image-size',
                    alt:   'image_alt'
                }, function( option, prop ) {
                    if ( display[ prop ] )
                        options[ option ] = display[ prop ];
                });
            } else if ( 'video' === obj_attachment.type ) {
                html = wp.media.string.video( display, obj_attachment );
            } else if ( 'audio' === obj_attachment.type ) {
                html = wp.media.string.audio( display, obj_attachment );
            } else {
                html = wp.media.string.link( display );
                options.post_title = display.title;
            }

            //attach info to attachment.attributes object
            attachment.attributes['nonce'] = wp.media.view.settings.nonce.sendToEditor;
            attachment.attributes['attachment'] = options;
            attachment.attributes['html'] = html;
            attachment.attributes['post_id'] = wp.media.view.settings.post.id;

            $('#'+key).val(JSON.stringify(options));
            console.log($('#'+key).val())
            $('#'+key+'-button img').remove();
            $('#'+key+'-button').append($('<img src="'+options['url']+'" alt="'+options['image_alt']+'" />'));
            $('#'+key+'-button span').hide();
            $('#'+key+'-reset').show();

            console.log(JSON.stringify(attachment))

            //do what ever you like to use it
            console.log(attachment.attributes);
            console.log(attachment.attributes['attachment']);
            console.log(attachment.attributes['html']);
        });
    });

//reset selection in popup, when open the popup
    frame.on('open',function() {
        var selection = frame.state('insert-image').get('selection');

        //remove all the selection first
        selection.each(function(image) {
            var attachment = wp.media.attachment( image.attributes.id );
            attachment.fetch();
            selection.remove( attachment ? [ attachment ] : [] );
        });

        //add back current selection, in here let us assume you attach all the [id] to <div id="my_file_group_field">...<input type="hidden" id="file_1" .../>...<input type="hidden" id="file_2" .../>
//        $("#my_file_group_field").find('input[type="hidden"]').each(function(){
        $('#'+key).each(function(){
            var input_id = $(this);
            if( input_id.val() ){
//                attachment = wp.media.attachment($.parseJSON(input_id.val()) );
                attachment = wp.media.attachment($.parseJSON(input_id.val()).id );
                attachment.fetch();
                selection.add( attachment ? [ attachment ] : [] );
            }
        });
    });


//now open the popup
    frame.open();
    });

    var sidiAlbum=$('.sidi-album');
    if(sidiAlbum.length){
        $(sidiAlbum).on('click', '.sidi-add-disc', function(){
            console.log('.sidi-add-disc');
            var sidiNewDisc=$('#sidi-new-disc',sidiAlbum);
            var newDisc=$(sidiNewDisc.html());
            var key=sidiAlbum.attr('id');
            sidiNewDisc.data('current', sidiNewDisc.data('current')+1);
            sidiNewDisc.data('new', sidiNewDisc.data('new')+1);
            var discNumber=sidiNewDisc.data('current'),
                discNew=sidiNewDisc.data('new'),
                idNew=key+'-'+ discNew
            $(newDisc).attr('id', idNew)
            $('p label b span',newDisc).first().text(discNumber)
            $('.sidi-disc-number',newDisc).val(discNumber).attr('id',idNew+'-0-disc').attr('name',idNew+'-0-disc')

            $( ".sortable", newDisc).sortable({
                placeholder: "ui-state-highlight"
            });
            sidiAlbum.append(newDisc);

        })
        $(sidiAlbum).on('click','.sidi-del-disc', function(){
            console.log('.sidi-del-disc');
            var parent=$(this).parent(),
                i= 0;
            parent.remove();
            var sidiNewDisc=$('#sidi-new-disc',sidiAlbum);
            sidiNewDisc.data('current', sidiNewDisc.data('current')-1);
            $('.sidi-discs',sidiAlbum).filter(":visible").each(function(){
                var self=$(this);
                i++;
                $('p label b span',self).first().text(i);
                $('.sidi-disc-number',self).val(i);

                var tabindex=i*100;
                $('.sidi-track',self).filter(":visible").each(function(){
                    var self=$(this);
                    $('.sidi-track-title',self).attr('tabindex',++tabindex);
                    $('.sidi-track-time',self).attr('tabindex',++tabindex);
                })
            })
        })
        $(sidiAlbum).on('click', '.sidi-add-track', function(){
            console.log('.sidi-add-track');
//            console.log($(this).parent('div'));
            var disc=$(this).parent(),
                idDisc=disc.attr('id'),
                sidiNewTrack=$('.sidi-new-track',disc),
                trackNumber=sidiNewTrack.data('current')+1,
                trackNew=sidiNewTrack.data('new')+1,
                idNew=idDisc+'-'+ trackNew,
                newTrack=$($.trim(sidiNewTrack.html())),
                tabindex=( $('.sidi-disc-number',disc).val()  *100)+(trackNumber-1)*2;
            sidiNewTrack.data('current',trackNumber)
            sidiNewTrack.data('new',trackNew)
            newTrack.attr('id',idNew);
            $('.sidi-track-track',newTrack).val(trackNumber).attr('id',idNew+'-track').attr('name',idNew+'-track').disableSelection()
            $('.sidi-track-num',newTrack).text(trackNumber).attr('id',idNew+'-num').attr('name',idNew+'-num').disableSelection()
            $('.sidi-track-title',newTrack).attr('id',idNew+'-title').attr('name',idNew+'-title').attr('tabindex',++tabindex)
            $('.sidi-track-time',newTrack).attr('id',idNew+'-time').attr('name',idNew+'-time').attr('tabindex',++tabindex)

            $('.sidi-tracks',disc).append(newTrack);
            console.log($('#'+idNew+' input.sidi-track-time'));
        })
        $(sidiAlbum).on('click', '.sidi-del-track', function(){
            console.log('.sidi-del-track');
            var parent=$(this).parent(),
                disc=parent.parent(),
                i= 0;
            var tabindex=$(this).closest('.sidi-discs').find('.sidi-disc-number').val()*100;
            parent.remove();
            var sidiNewTrack=$('.sidi-new-track',disc.parent());
            sidiNewTrack.data('current', sidiNewTrack.data('current')-1);
            $('.sidi-track',disc).filter(":visible").each(function(){
                var self=$(this);
                i++;
                $('.sidi-track-track',self).val(i);
                $('.sidi-track-num',self).text(i);
                $('.sidi-track-title',self).attr('tabindex',++tabindex);
                $('.sidi-track-time',self).attr('tabindex',++tabindex);
            })
        })
        $(sidiAlbum ).on( "sortstop",".sortable", function( event, ui ) {
            console.log($(this))
            var i=0;

            var tabindex=$(this).closest('.sidi-discs').find('.sidi-disc-number').val()*100;
            $('.sidi-track',$(this)).filter(":visible").each(function(){
                var self=$(this);
                i++;
                $('.sidi-track-track',self).val(i);
                $('.sidi-track-num',self).text(i);
                $('.sidi-track-title',self).attr('tabindex',++tabindex);
                $('.sidi-track-time',self).attr('tabindex',++tabindex);
            })
        } );
        $( ".sortable", sidiAlbum).sortable({
            handle: ".sidi-track-num",
            placeholder: "ui-state-highlight"
        });
    }
});
