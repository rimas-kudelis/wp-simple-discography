/**
 * Created by sebastien on 16/08/2014.
 */
jQuery(document).ready(function ($) {
    var number= $('number')
    $(document).on('keydown', 'input[type=number]',function(e){
        var k=e.key,t=this;
        var p = e.which ? e.which : e.keyCode ;
        if (p === 8 // backspace
            || p === 46 // delete
            || (p >= 35 && p <= 40) // end, home, arrows
        // TODO: shift, ctrl, alt, caps-lock, etc
            )
           return;

        if (k.match(/[^0-9\-]/g) != null) {
            e.preventDefault();
        }else{
            if(k=='-' && t.value.length >0)
                e.preventDefault();
        }
    }).on('keyup change', 'input[type=number]',function(e){
        var p = e.which ? e.which : e.keyCode ;
        if (p === 8 // backspace
            || p === 46 // delete
            || (p >= 35 && p <= 40) // end, home, arrows
        // TODO: shift, ctrl, alt, caps-lock, etc
            )
            return;

        var s = $(this),
            a = parseInt(s.attr('max')),
            i = parseInt(s.attr('min')),
            s = this,
            v= parseInt(s.value);
        if(s.value !==''){
            if((typeof a!=='undefined'&&v>a)||(typeof i!=='undefined'&&v<i)){
                v = v.toString();
                s.value = v.slice(0, v.length -1);
            }
        }
    });

});