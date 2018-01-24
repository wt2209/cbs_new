
window.audio = document.getElementById('audio');
function playSound() {
    window.audio.pause();
    window.audio.currentTime = 0;
    window.audio.play();
}
// 定时获取维修推送更新
window.getRepairNotify = function () {
    var url = $('#unreviewed').attr('url');
    var unreviewed = $('#unreviewed');
    var unprinted = $('#unprinted');
    var totalNotify = $('#totalNotify');
    $.get(url, function(data){
        if(data.username == 'admin' || data.username == 'houlei') {
            var oldNumber = Number(unreviewed.text());
            var newNumber = Number(data.unreviewed);
        } else {
            var oldNumber = Number(unprinted.text());
            var newNumber = Number(data.unprinted);
        }
        if (oldNumber < newNumber) {
            playSound()
        }

        unreviewed.text(data.unreviewed);
        unprinted.text(data.unprinted);
        unreviewed.show();
        unprinted.show();
        if (data.unreviewed == 0) {
            unreviewed.hide();
        }
        if (data.unprinted == 0) {
            unprinted.hide();
        }
        totalNotify.text(data.unreviewed + data.unprinted)
        totalNotify.show();
        if (data.unreviewed + data.unprinted == 0) {
            totalNotify.hide();
        }
    }, 'json')
}
window.getRepairNotify();
setInterval(window.getRepairNotify, 1000*60*5)