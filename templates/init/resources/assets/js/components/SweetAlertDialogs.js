import swal from 'sweetalert2';

function alert (text) {
  return swal({
    title: "错误",
    text: text,
    type: "error"
  });
}
function confirm (text) {
  return swal({
    title: "确认操作",
    text: text,
    type: "question",
    confirmButtonText: '确认',
    showCancelButton: true,
    cancelButtonText: "取消"
  });
}
function prompt (text) {
}

export {alert, confirm, prompt};