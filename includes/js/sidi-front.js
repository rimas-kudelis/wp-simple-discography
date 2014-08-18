/**
 * Created by sebastien on 04/08/2014.
 */


jQuery(document).ready(function ($) {
    var sidi=$('.sidi');
    if(sidi.length){
        var thumbnail=$('.sidi-thumbnail', sidi);

        var currentUrl='',
            uid = 0;
        console.log(currentUrl);
        var pushState = function(url){
//            console.log('pushState' + url);
            if (url != currentUrl && currentUrl.length){
                if (typeof(history) != 'undefined' && typeof(history.pushState) != 'undefined') {
                    uid++;
                    history.pushState({"html":url, uid:uid}, url, url);
                    document.title =url;
                } else {
                    window.location = '#' + url ;
                }
            }
            currentUrl = url;
        };
        var popstate = ($.browser.webkit)?false:true;
        $(window).bind('popstate', function(event) {
            if(popstate){
                if (currentUrl.length){
                    if (!event.originalEvent.state || event.originalEvent.state.uid < uid) {
                        if (event.originalEvent.state) {
                            uid = event.originalEvent.state.uid;
                        } else {
                            uid = 0;
                        }
                        location.reload();
                    } else {
                        uid = event.originalEvent.state.uid;
                        location.reload();
                    }
                }
            }else{
                popstate=true;
            }
        });
        var sidiSilde = function(clicked, div, scrollTo, addFunction){
            var classShow='discs-show',
                classHidden='discs-hidden';
            var disk=$(clicked).closest('.sidi-album'),
                url=$(clicked).attr('href'),
                pg=$(clicked).closest('.sidi').attr('id');
            if(typeof pg ==='undefined' || pg.length==0)
                pg=''
            else{
                pg=pg.replace(/^sidi\-/i,''),
                pg+=pg.length?'_':'';
            }
            var pathname = window.location.pathname;
            var re = new RegExp("(\\?|&)"+pg.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&")+"alb=[0-9]+", "gi");
            var s=location.search.replace(re, "");
            if(s[0]=='&')
                s = '?'+s.substring(1);
            url=location.origin+location.pathname+location.hash

            if(disk.hasClass(classShow)){
                $(div,disk).slideUp(400)
                disk.removeClass(classShow).addClass(classHidden);
                if(thumbnail.length)
                    disk.height('')

                pushState(url+s);
            }else{
                disk.parent().find('.'+classShow).each(function(){
                    var self=$(this);
                    $(div,self).slideUp(400)
                    self.removeClass(classShow).addClass(classHidden);
                    if(thumbnail.length)
                        self.height('')
                })
                $(div,disk).slideDown(400, function(){
                    if(typeof addFunction!=="undefined")
                        addFunction(disk);
                    if(typeof scrollTo!=="undefined")
                        $.scrollTo($(scrollTo, disk),400);
                })
                disk.removeClass(classHidden).addClass(classShow);
                var id = $(clicked).closest('.sidi-album').attr('id').replace(/^sidi-/i,'');

                s=s+'&'+pg+'alb'+'='+id;
                if(s[0]=='&')
                    s = '?'+s.substring(1);
                pushState(url+s);
            }

        }
        pushState(document.referrer?document.referrer:'-');

        $('.sidi-list',sidi).on('click','.sidi-cover-link', function(e){
            e.preventDefault();
            e.stopPropagation();
            sidiSilde(this, '.sidi-discs','.sidi-content');
        })

        if(thumbnail.length){
            var resizeContent = function(){
                var widthUl=thumbnail.width(),
                    cover=$('.sidi-album', thumbnail).first(),
                    widthCover=cover.outerWidth(true),
                    nbCoverr=Math.floor(widthUl/widthCover);
//            console.log(nbCoverr, widthUl,widthCover, widthUl/widthCover);
                if(widthUl>=(2*widthCover)){
                    var margingCover=widthCover-cover.innerWidth(),
                        widthContent=(nbCoverr*widthCover)-margingCover;
                    $('.sidi-content', thumbnail).width(widthContent).css({left:'50%', marginLeft: -widthContent/2})
                }else{
                    $('.sidi-content', thumbnail).width("100%").css({left:'non', marginLeft: '0'})
                }
                var visibelAlbum= $('.discs-show', thumbnail)
                if(visibelAlbum.length)
                    resizeAmbum(visibelAlbum);
            };
            var resizeAmbum = function(album){
                if(typeof album !== 'undefined')
                    album.height($('.sidi-content', album).outerHeight()+$('.sidi-cover', album).outerHeight(true)+$('.sidi-thumbnail-title', album).outerHeight(true));
            };
            resizeContent();
            $(window).resize(function () {
                resizeContent();
            });
            thumbnail.on('click','.sidi-cover-link', function(e){
                e.preventDefault();
                e.stopPropagation();
                sidiSilde(this, '.sidi-content', '.sidi-cover',resizeAmbum);
            });

        }
    }
})