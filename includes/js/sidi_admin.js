/**
 * Created by sebastien on 27/07/2014.
 */

jQuery(document).ready(function ($) {
// Check to make sure the input box exists
    var datepicker=$('.datepicker')
    if( 0 < datepicker.length ) {
//        datepicker.datepicker({
//            dateFormat : 'dd/mm/yy'
//        });
        datepicker.datepicker({
            // Show the 'close' and 'today' buttons
            showButtonPanel: true,
            closeText: objectL10n.closeText,
            currentText: objectL10n.currentText,
            monthNames: objectL10n.monthNames,
            monthNamesShort: objectL10n.monthNamesShort,
            dayNames: objectL10n.dayNames,
            dayNamesShort: objectL10n.dayNamesShort,
            dayNamesMin: objectL10n.dayNamesMin,
            dateFormat: objectL10n.dateFormat,
            firstDay: objectL10n.firstDay,
            isRTL: objectL10n.isRTL,
        });
    } // end if
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
        $(sidiAlbum).on('click', '.sidi-add-disk', function(){
            console.log('.sidi-add-disk');
            var sidiNewDisk=$('#sidi-new-disk',sidiAlbum);
            var newDisk=$(sidiNewDisk.html());
            var key=sidiAlbum.attr('id');
            sidiNewDisk.data('current', sidiNewDisk.data('current')+1);
            sidiNewDisk.data('new', sidiNewDisk.data('new')+1);
            var diskNumber=sidiNewDisk.data('current'),
                diskNew=sidiNewDisk.data('new'),
                idNew=key+'-'+ diskNew
            $(newDisk).attr('id', idNew)
            $('p label b span',newDisk).first().text(diskNumber)
            $('.sidi-disk-number',newDisk).val(diskNumber).attr('id',idNew+'-0-disk').attr('name',idNew+'-0-disk')

            $( ".sortable", newDisk).sortable({
                placeholder: "ui-state-highlight"
            });
            sidiAlbum.append(newDisk);

        })
        $(sidiAlbum).on('click','.sidi-del-disk', function(){
            console.log('.sidi-del-disk');
            var parent=$(this).parent(),
                i= 0;
            parent.remove();
            var sidiNewDisk=$('#sidi-new-disk',sidiAlbum);
            sidiNewDisk.data('current', sidiNewDisk.data('current')-1);
            $('.sidi-discs',sidiAlbum).filter(":visible").each(function(){
                var self=$(this);
                i++;
                $('p label b span',self).first().text(i);
                $('.sidi-disk-number',self).val(i);

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
            var disk=$(this).parent(),
                idDisk=disk.attr('id'),
                sidiNewTrack=$('.sidi-new-track',disk),
                trackNumber=sidiNewTrack.data('current')+1,
                trackNew=sidiNewTrack.data('new')+1,
                idNew=idDisk+'-'+ trackNew,
                newTrack=$($.trim(sidiNewTrack.html())),
                tabindex=( $('.sidi-disk-number',disk).val()  *100)+(trackNumber-1)*2;
            sidiNewTrack.data('current',trackNumber)
            sidiNewTrack.data('new',trackNew)
            newTrack.attr('id',idNew);
            $('.sidi-track-track',newTrack).val(trackNumber).attr('id',idNew+'-track').attr('name',idNew+'-track').disableSelection()
            $('.sidi-track-num',newTrack).text(trackNumber).attr('id',idNew+'-num').attr('name',idNew+'-num').disableSelection()
            $('.sidi-track-title',newTrack).attr('id',idNew+'-title').attr('name',idNew+'-title').attr('tabindex',++tabindex)
            $('.sidi-track-time',newTrack).attr('id',idNew+'-time').attr('name',idNew+'-time').attr('tabindex',++tabindex)

            $('.sidi-traks',disk).append(newTrack);
            console.log($('#'+idNew+' input.sidi-track-time'));

            $('#'+idNew+' input.sidi-track-time').timepicker({
                controlType: myControl,
                minuteMax: 999,
                showHour: false,
                showSecond: true,
                timeFormat: 'mm:ss'
        });


        })
        $(sidiAlbum).on('click', '.sidi-del-track', function(){
            console.log('.sidi-del-track');
            var parent=$(this).parent(),
                disk=parent.parent(),
                i= 0;
            var tabindex=$(this).closest('.sidi-discs').find('.sidi-disk-number').val()*100;
            parent.remove();
            var sidiNewTrack=$('.sidi-new-track',disk.parent());
            sidiNewTrack.data('current', sidiNewTrack.data('current')-1);
            $('.sidi-track',disk).filter(":visible").each(function(){
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

            var tabindex=$(this).closest('.sidi-discs').find('.sidi-disk-number').val()*100;
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
//        $( ".sidi-track-num" ).disableSelection();
        var myControl=  {
            create: function(tp_inst, obj, unit, val, min, max, step){
                $('<input class="ui-timepicker-input" value="'+val+'" style="width:50%">')
                    .appendTo(obj)
                    .spinner({
                        min: min,
                        max: max,
                        step: step,
                        change: function(e,ui){ // key events
                            // don't call if api was used and not key press
                            if(e.originalEvent !== undefined)
                                tp_inst._onTimeChange();
                            tp_inst._onSelectHandler();
                        },
                        spin: function(e,ui){ // spin events
                            tp_inst.control.value(tp_inst, obj, unit, ui.value);
                            tp_inst._onTimeChange();
                            tp_inst._onSelectHandler();
                        }
                    });
                return obj;
            },
            options: function(tp_inst, obj, unit, opts, val){
                if(typeof(opts) == 'string' && val !== undefined)
                    return obj.find('.ui-timepicker-input').spinner(opts, val);
                return obj.find('.ui-timepicker-input').spinner(opts);
            },
            value: function(tp_inst, obj, unit, val){
                if(val !== undefined)
                    return obj.find('.ui-timepicker-input').spinner('value', val);
                return obj.find('.ui-timepicker-input').spinner('value');
            }
        };

        $('input.sidi-track-time:visible').timepicker({
            controlType: myControl,
            minuteMax: 999,
            showHour: false,
            showSecond: true,
            timeFormat: 'mm:ss'
        });
    }
});
