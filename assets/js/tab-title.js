/**
 * Shiro Tab Title - Dynamic title on visibility change
 * Loads on all pages (index, post, page, archive, etc.)
 */
;(function () {
    'use strict';

    var originalTitle = document.title;
    var leaveMessages = [
        '君已离席，墨未干……',
        '山高水远，后会有期',
        '客从何处来，又往何处去',
        '风起云涌，静候归来',
        '纸短情长，待君重阅',
        '灯火阑珊处，不见故人',
        '一别两宽，各自珍重',
        '此去经年，应是良辰好景'
    ];
    var backMessages = [
        '幸得重逢，请继续',
        '故人归来，茶尚温',
        '久别重逢，不胜欢喜',
        '风尘仆仆，且坐且读',
        '归来仍是读书人',
        '重逢亦如初见',
        '山水有相逢',
        '别来无恙，请续前文'
    ];

    var titleTimer = null;

    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            var msg = leaveMessages[Math.floor(Math.random() * leaveMessages.length)];
            document.title = msg;
        } else {
            var msg = backMessages[Math.floor(Math.random() * backMessages.length)];
            document.title = msg;
            clearTimeout(titleTimer);
            titleTimer = setTimeout(function () {
                document.title = originalTitle;
            }, 2000);
        }
    });
})();
