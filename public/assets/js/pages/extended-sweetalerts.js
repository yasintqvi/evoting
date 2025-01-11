const basicButton = document.getElementById("sweetalert-basic"),
  titleButton = document.getElementById("sweetalert-title"),
  longcontentButton = document.getElementById("sweetalert-longcontent"),
  confirmButton = document.getElementById("sweetalert-confirm-button"),
  paramsButton = document.getElementById("sweetalert-params"),
  imageButton = document.getElementById("sweetalert-image"),
  closeButton = document.getElementById("sweetalert-close"),
  positionTopStart = document.querySelector("#position-top-start"),
  positionTopEnd = document.querySelector("#position-top-end"),
  positionBottomStart = document.querySelector("#position-bottom-start"),
  positionBottomEnd = document.querySelector("#position-bottom-end"),
  infoButton = document.getElementById("sweetalert-info"),
  warningButton = document.getElementById("sweetalert-warning"),
  errorButton = document.getElementById("sweetalert-error"),
  successButton = document.getElementById("sweetalert-success"),
  questionButton = document.getElementById("sweetalert-question");
infoButton.addEventListener("click", (t) => {
  Swal.fire({
    text: "در اینجا نمونه ای از اطلاعات سوییت الرت!",
    icon: "info",
    buttonsStyling: !1,
    confirmButtonText: "متوجه شدم!",
    customClass: { confirmButton: "btn btn-info" },
  });
}),
  warningButton.addEventListener("click", (t) => {
    Swal.fire({
      text: "در اینجا نمونه ای از هشدار دهنده سوییت الرت!",
      icon: "warning",
      buttonsStyling: !1,
      confirmButtonText: "متوجه شدم!",
      customClass: { confirmButton: "btn btn-warning" },
    });
  }),
  errorButton.addEventListener("click", (t) => {
    Swal.fire({
      text: "در اینجا نمونه ای از خطای سوییت الرت!",
      icon: "error",
      buttonsStyling: !1,
      confirmButtonText: "متوجه شدم!",
      customClass: { confirmButton: "btn btn-danger" },
    });
  }),
  successButton.addEventListener("click", (t) => {
    Swal.fire({
      text: "در اینجا نمونه ای از موفقیت سوییت الرت!",
      icon: "success",
      buttonsStyling: !1,
      confirmButtonText: "متوجه شدم!",
      customClass: { confirmButton: "btn btn-success" },
    });
  }),
  questionButton.addEventListener("click", (t) => {
    Swal.fire({
      text: "در اینجا نمونه ای از یک سوال سوئیت الرت!",
      icon: "question",
      buttonsStyling: !1,
      confirmButtonText: "متوجه شدم!",
      customClass: { confirmButton: "btn btn-primary" },
    });
  }),
  positionTopStart &&
    (positionTopStart.onclick = function () {
      Swal.fire({
        position: "top-start",
        icon: "success",
        text: "کار شما ذخیره شده است",
        showConfirmButton: !1,
        timer: 1500,
        customClass: { confirmButton: "btn btn-primary" },
        buttonsStyling: !1,
      });
    }),
  positionTopEnd &&
    (positionTopEnd.onclick = function () {
      Swal.fire({
        position: "top-end",
        icon: "success",
        text: "کار شما ذخیره شده است",
        showConfirmButton: !1,
        timer: 1500,
        customClass: { confirmButton: "btn btn-primary" },
        buttonsStyling: !1,
      });
    }),
  positionBottomStart &&
    (positionBottomStart.onclick = function () {
      Swal.fire({
        position: "bottom-start",
        icon: "success",
        text: "کار شما ذخیره شده است",
        showConfirmButton: !1,
        timer: 1500,
        customClass: { confirmButton: "btn btn-primary" },
        buttonsStyling: !1,
      });
    }),
  positionBottomEnd &&
    (positionBottomEnd.onclick = function () {
      Swal.fire({
        position: "bottom-end",
        icon: "success",
        text: "کار شما ذخیره شده است",
        showConfirmButton: !1,
        timer: 1500,
        customClass: { confirmButton: "btn btn-primary" },
        buttonsStyling: !1,
      });
    }),
  basicButton.addEventListener("click", (t) => {
    Swal.fire({
      title:"هر احمق می تواند از رایانه استفاده کند",
      customClass: { confirmButton: "btn btn-primary mt-2" },
      buttonsStyling: !1,
    });
  }),
  titleButton.addEventListener("click", function () {
    Swal.fire({
      title:"اینترنت؟",
      text: "این چیز هنوز در اطراف است؟",
      icon: "question",
      customClass: { confirmButton: "btn btn-primary mt-2" },
      buttonsStyling: !1,
      showCloseButton: !0,
    });
  }),
  successButton.addEventListener("click", function () {
    Swal.fire({
      title:"کار خوب!",
      text: "شما روی دکمه کلیک کردید!",
      icon: "success",
      showCancelButton: !0,
      customClass: {
        confirmButton: "btn btn-primary me-2 mt-2",
        cancelButton: "btn btn-danger mt-2",
      },
      buttonsStyling: !1,
      showCloseButton: !0,
    });
  }),
  errorButton.addEventListener("click", function () {
    Swal.fire({
      title:"اوه ...",
      text: "مشکلی پیش آمد!",
      icon: "error",
      customClass: { confirmButton: "btn btn-primary mt-2" },
      buttonsStyling: !1,
      footer: '<a href="#!"> چرا این مسئله را دارم؟</a>',
      showCloseButton: !0,
    });
  }),
  longcontentButton.addEventListener("click", function () {
    Swal.fire({
      imageUrl: "https://placeholder.pics/svg/300x1500",
      imageHeight: 1500,
      imageAlt: "یک تصویر بلند",
      customClass: { confirmButton: "btn btn-primary mt-2" },
      buttonsStyling: !1,
      showCloseButton: !0,
    });
  }),
  confirmButton.addEventListener("click", function () {
    Swal.fire({
      title:"مطمئن هستی؟",
      text: "شما نمی توانید این را برگردانید!",
      icon: "warning",
      showCancelButton: !0,
      customClass: {
        confirmButton: "btn btn-primary me-2 mt-2",
        cancelButton: "btn btn-danger mt-2",
      },
      confirmButtonText: "بله ، آن را حذف کنید!",
      buttonsStyling: !1,
      showCloseButton: !0,
    }).then(function (t) {
      t.value &&
        Swal.fire({
          title:"حذف شد!",
          text: "پرونده شما حذف شده است.",
          icon: "success",
          customClass: { confirmButton: "btn btn-primary mt-2" },
          buttonsStyling: !1,
        });
    });
  }),
  paramsButton.addEventListener("click", function () {
    Swal.fire({
      title:"مطمئن هستی؟",
      text: "شما نمی توانید این را برگردانید!",
      icon: "warning",
      showCancelButton: !0,
      confirmButtonText: "بله ، آن را حذف کنید!",
      cancelButtonText: "نه ، لغو!",
      customClass: {
        confirmButton: "btn btn-primary me-2 mt-2",
        cancelButton: "btn btn-danger mt-2",
      },
      buttonsStyling: !1,
      showCloseButton: !0,
    }).then(function (t) {
      t.value
        ? Swal.fire({
            title:"حذف شد!",
            text: "پرونده شما حذف شده است.",
            icon: "success",
            customClass: { confirmButton: "btn btn-primary mt-2" },
            buttonsStyling: !1,
          })
        : t.dismiss === Swal.DismissReason.cancel &&
          Swal.fire({
            title:"لغو شده",
            text: "پرونده خیالی شما ایمن است :)",
            icon: "error",
            customClass: { confirmButton: "btn btn-primary mt-2" },
            buttonsStyling: !1,
          });
    });
  }),
  imageButton.addEventListener("click", function () {
    Swal.fire({
      title:"شیرین!",
      text: "مودال با یک تصویر سفارشی.",
      imageUrl: "assets/images/logo-sm.png",
      imageHeight: 40,
      customClass: { confirmButton: "btn btn-primary mt-2" },
      buttonsStyling: !1,
      showCloseButton: !0,
    });
  }),
  closeButton.addEventListener("click", function () {
    var t;
    Swal.fire({
      title:"هشدار نزدیک خودکار!",
      html: "من بسته خواهم شد<strong></strong> ثانیه.",
      timer: 2e3,
      timerProgressBar: !0,
      showCloseButton: !0,
      didOpen: function () {
        Swal.showLoading(),
          (t = setInterval(function () {
            var t = Swal.getHtmlContainer();
            t &&
              (t = t.querySelector("b")) &&
              (t.textContent = Swal.getTimerLeft());
          }, 100));
      },
      onClose: function () {
        clearInterval(t);
      },
    }).then(function (t) {
      t.dismiss === Swal.DismissReason.timer &&
        console.log("من توسط تایمر بسته شدم");
    });
  }),
  document
    .getElementById("custom-html-alert")
    .addEventListener("click", function () {
      Swal.fire({
        title:"<i> اچ تی ام ال </i> <u> مثال </u>",
        icon: "info",
          html: 'می توانید استفاده کنید <b>متن بزرگ</b>, <a href="#">لینک ها</a> و سایر برچسب های اچ تی ام ال ',
        showCloseButton: !0,
        showCancelButton: !0,
        customClass: {
          confirmButton: "btn btn-success me-2",
          cancelButton: "btn btn-danger",
        },
        buttonsStyling: !1,
        confirmButtonText: '<i class="ti ti-thumb-up-filled me-1"></i> عالی!',
        cancelButtonText: '<i class="ti ti-thumb-down-filled"></i>',
      });
    }),
  document
    .getElementById("custom-padding-width-alert")
    .addEventListener("click", function () {
      Swal.fire({
        title:"عرض سفارشی ، بالشتک ، پس زمینه.",
        width: 600,
        padding: 100,
        customClass: { confirmButton: "btn btn-primary" },
        buttonsStyling: !1,
        background:
          "var(--osen-secondary-bg) url(assets/images/small/small-5.jpg) no-repeat center",
      });
    }),
  document.getElementById("ajax-alert").addEventListener("click", function () {
    Swal.fire({
      title:"برای اجرای درخواست اجکس ایمیل ارسال کنید",
      input: "email",
      showCancelButton: !0,
      confirmButtonText: "ارسال کردن",
      showLoaderOnConfirm: !0,
      customClass: {
        confirmButton: "btn btn-primary me-2",
        cancelButton: "btn btn-danger",
      },
      buttonsStyling: !1,
      showCloseButton: !0,
      preConfirm: function (e) {
        return new Promise(function (t, n) {
          setTimeout(function () {
            "taken@example.com" === e ? n("این ایمیل قبلاً گرفته شده است.") : t();
          }, 2e3);
        });
      },
      allowOutsideClick: !1,
    }).then(function (t) {
      Swal.fire({
        icon: "success",
        title:"درخواست آژاکس به پایان رسید!",
        customClass: { confirmButton: "btn btn-primary" },
        buttonsStyling: !1,
        html: "ایمیل ارسال شده: " + t,
      });
    });
  });
