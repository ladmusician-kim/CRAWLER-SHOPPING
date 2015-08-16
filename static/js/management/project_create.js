/**
 * Created by ladmusician on 2015-07-30.
 */
/* test/index.js */
$(function() {
    //전역변수선언
    var editor_object = [];
    nhn.husky.EZCreator.createInIFrame({
        oAppRef: editor_object,
        elPlaceHolder: "smarteditor",
        sSkinURI: "/CRAWLER/library/package/smarteditor/SmartEditor2Skin.html",
        htParams : {
            // 툴바 사용 여부 (true:사용/ false:사용하지 않음)
            bUseToolbar : true,
            // 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
            bUseVerticalResizer : true,
            // 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
            bUseModeChanger : true,
        }
    });

//전송버튼 클릭이벤트
$("#ng-submit").click(function(){
    //id가 smarteditor인 textarea에 에디터에서 대입
    editor_object.getById["smarteditor"].exec("UPDATE_CONTENTS_FIELD", []);

    // 이부분에 에디터 validation 검증

    //폼 submit
    $("#frm").submit();
});

$('.ng-modal-admin-btn').click(function () {
    getJson('/CRAWLER/API/Management/get_users', {},
        function (data) {
            $($('#ng-user-list')[0]).html(data);

            $('#ng-user-list .ng-user-item').click(function () {
                var userid = $(this).children('input').val();
                var username = $(this).children('div').text();
                console.log(username);
                $('#ng-admin-userid').val(userid);
                $('.ng-admin-username').text(username);
            });
        }, function (arg) {
            alert('사용자를 불러오는데 오류가 발생했습니다');
        }, "html");
});
});



