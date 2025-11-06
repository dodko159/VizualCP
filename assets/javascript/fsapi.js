var fsapi
fsapi = (function (undefined) {
    var dom, enter, exit, fullscreen

    // support for entering fullscreen
    dom = document.createElement('img')
    if ('requestFullscreen' in dom) {
        enter = 'requestFullscreen' // W3C proposal
    }
    else if ('requestFullScreen' in dom) {
        enter = 'requestFullScreen' // mozilla proposal
    }
    else if ('webkitRequestFullScreen' in dom) {
        enter = 'webkitRequestFullScreen' // webkit
    }
    else if ('mozRequestFullScreen' in dom) {
        enter = 'mozRequestFullScreen' // firefox
    }
    else {
        enter = undefined // not supported in this browser
    }

    // support for exiting fullscreen
    if ('exitFullscreen' in document) {
        exit = 'exitFullscreen' // W3C proposal
    }
    else if ('cancelFullScreen' in document) {
        exit = 'cancelFullScreen' // mozilla proposal
    }
    else if ('webkitCancelFullScreen' in document) {
        exit = 'webkitCancelFullScreen' // webkit
    }
    else if ('mozCancelFullScreen' in document) {
        exit = 'mozCancelFullScreen' // firefox
    }
    else {
        exit = undefined // not supported in this browser
    }

    // support for detecting when in fullscreen
    if ('fullscreen' in document) {
        fullscreen = 'fullscreen' // W3C proposal
    }
    else if ('fullScreen' in document) {
        fullscreen = 'fullScreen' // mozilla proposal
    }
    else if ('webkitIsFullScreen' in document) {
        fullscreen = 'webkitIsFullScreen' // webkit
    }
    else if ('mozFullScreen' in document) {
        fullscreen = 'mozFullScreen' // firefox
    }
    else {
        fullscreen = undefined // not supported in this browser
    }

    return {
        enter      : enter,
        exit       : exit,
        fullscreen : fullscreen
    }
}())

$(document).ready(function(){
    btn = document.getElementById('full_screen_btn');

    if (fsapi.enter && fsapi.exit && fsapi.fullscreen) {
        btn.style.display = "block";

        btn.addEventListener('click', function (evt) {
            var element
            element = document.getElementsByTagName("BODY")[0];
            //element = document.getElementById('content_back');
            if (document[fsapi.fullscreen]) {
                document[fsapi.exit]()
                
            }
            else {
                element[fsapi.enter]()
                
            }
        }, false)
    }else{
        btn.style.display = "none";
    }
});
