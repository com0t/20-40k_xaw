function fbrev_popup(url, width, height, cb) {
    var top = top || (screen.height/2)-(height/2),
        left = left || (screen.width/2)-(width/2),
        win = window.open(url, '', 'location=1,status=1,resizable=yes,width='+width+',height='+height+',top='+top+',left='+left);
    function check() {
        if (!win || win.closed != false) {
            cb();
        } else {
            setTimeout(check, 100);
        }
    }
    setTimeout(check, 100);
}

function fbrev_connect(el, data) {

    var temp_code = fbrev_randstr(16);
    fbrev_popup('https://app.widgetpack.com/auth/fbrev?scope=pages_show_list,pages_read_user_content,pages_read_engagement&state=' + temp_code, 670, 520, function() {
        WPacXDM.get('https://embed.widgetpack.com', 'https://app.widgetpack.com/widget/facebook/accesstoken?temp_code=' + temp_code, {}, function(res) {
            WPacFastjs.jsonp('https://graph.facebook.com/me/accounts', {access_token: res.accessToken, limit: 250}, function(res) {

                var pagesEl = el.querySelector('.fbrev-pages');

                WPacFastjs.each(res.data, function(page) {
                    var pageEL = WPacFastjs.create('div', 'fbrev-page');
                    pageEL.innerHTML = '<img src="https://graph.facebook.com/' + page.id +  '/picture" class="fbrev-page-photo">' +
                                       '<span>' + page.name + '</span>';
                    pagesEl.appendChild(pageEL);
                    WPacFastjs.on(pageEL, 'click', function() {
                        return fbrev_page_click(el, pageEL, page, data.cb);
                    });
                });

                if (res.data.length < 2) {
                    var page = res.data[0];
                    var pageEL = pagesEl.querySelector('.fbrev-page');
                    if (pageEL) {
                        fbrev_page_click(el, pageEL, page, data.cb);
                    }
                }
            });
        });
    });
    return false;
}

function fbrev_page_click(el, pageEL, page, cb) {
    var pagesEl          = el.querySelector('.fbrev-pages'),
        idEl             = el.querySelector('.fbrev-page-id'),
        nameEl           = el.querySelector('.fbrev-page-name'),
        tokenEl          = el.querySelector('.fbrev-page-token'),
        businessPhoto    = el.querySelector('.fbrev-business-photo'),
        businessPhotoImg = el.querySelector('.fbrev-business-photo-img');

    idEl.value = page.id;
    nameEl.value = page.name;
    tokenEl.value = page.access_token;
    jQuery(tokenEl).change();

    if (businessPhoto) {
        businessPhoto.value = '';
        businessPhotoImg.src = 'https://graph.facebook.com/' + page.id +  '/picture';
        WPacFastjs.show2(businessPhotoImg);
    }

    WPacFastjs.remcl(pagesEl.querySelector('.active'), 'active');
    WPacFastjs.addcl(pageEL, 'active');

    if (cb) cb();

    return false;
}

function fbrev_init(data) {

    var el = data.el;
    if (!el) return;

    var connectBtn = el.querySelector('.fbrev-connect');
    WPacFastjs.on(connectBtn, 'click', function() {
        fbrev_connect(el, data);
        return false;
    });

    jQuery(document).ready(function($) {

        var file_frame;
        $('.fbrev-page-photo-btn', el).on('click', function(e) {
            e.preventDefault();
            if (file_frame) {
                file_frame.open();
                return;
            }

            file_frame = wp.media.frames.file_frame = wp.media({
                title: $(this).data('uploader_title'),
                button: {text: $(this).data('uploader_button_text')},
                multiple: false
            });

            file_frame.on('select', function() {
                var place_photo_hidden = $('.fbrev-page-photo', el),
                    place_photo_img = $('.fbrev-page-photo-img', el);
                attachment = file_frame.state().get('selection').first().toJSON();
                place_photo_hidden.val(attachment.url);
                place_photo_img.attr('src', attachment.url);
                place_photo_img.show();

                // To make 'Save' button enable in the widget
                jQuery(place_photo_hidden).change();

                data.cb && data.cb();
            });
            file_frame.open();
            return false;
        });

        $('.rplg-toggle', el).unbind('click').click(function () {
            $(this).toggleClass('toggled');
            $(this).next().slideToggle();
        });
    });
}

function fbrev_randstr(len) {
   var result = '',
       chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
       charsLen = chars.length;
   for ( var i = 0; i < len; i++ ) {
      result += chars.charAt(Math.floor(Math.random() * charsLen));
   }
   return result;
}