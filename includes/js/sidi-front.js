/**
 * Created by sebastien on 04/08/2014.
 */


jQuery(document).ready(function ($) {
    var sidi=$('.sidi');
    if(sidi.length){

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
        pushState(document.referrer?document.referrer:'-');

        $(sidi).on('click','.sidi-cover-link', function(e){
            e.preventDefault();
            e.stopPropagation();
            var classShow='discs-show',
                classHidden='discs-hidden';
            var disk=$(this).closest('.sidi-album'),
                url=$(this).attr('href');
            if(disk.hasClass(classShow)){
                $('.sidi-discs',disk).slideUp(300)
                disk.removeClass(classShow).addClass(classHidden);
                var pathname = window.location.pathname;
                var s=location.search.replace(/(\?|&)alb=[0-9]+/ig, "");
                if(s[0]=='&')
                    s = '?'+s.substring(1);
                pushState(location.origin+location.pathname+location.hash+s);
            }else{
                disk.parent().find('.'+classShow).each(function(){
                    var self=$(this);
                    $('.sidi-discs',self).slideUp(300)
                    self.removeClass(classShow).addClass(classHidden);
                })
                $('.sidi-discs',disk).slideDown(300, function(){
                    $.scrollTo($('.sidi-content', disk),300);
                })
                disk.removeClass(classHidden).addClass(classShow);
                pushState(url);
            }
        })
    }
})