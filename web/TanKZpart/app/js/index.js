(function () {
    emailjs.init("FfRP8_grXtjwuFmX2");
})();

document.getElementById("emailSubmit").addEventListener("click", function () {
    document.getElementById("layerEM1").style.display = "block";
});

document.getElementById("PhoneNumberSubmit").addEventListener("click", function () {
    document.getElementById("layerPN1").style.display = "block";
});

document.getElementById("comfirmNoEmail").addEventListener("click", function (event) {
    event.preventDefault();
    document.getElementById("layerEM1").style.display = "none";
});

document.getElementById("comfirmNoPN").addEventListener("click", function (event) {
    event.preventDefault();
    document.getElementById("layerPN1").style.display = "none";
});

document.getElementById("comfirmYesEmail").addEventListener("click", function (event) {
    event.preventDefault(); // 阻止表单默认提交
    let emailInput = document.getElementById("emailInput").value;
    let emailFormat = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    // 验证邮箱格式
    if (!emailFormat.test(emailInput)) {
        alert("Invalid email format! \n Format: name@gmail.com");
        return;
    }

    // 检查邮箱是否被使用
    fetch(`app/page/saveEmail.php?email=${encodeURIComponent(emailInput)}&check=true`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.exists) {
            alert("This email is already in use. Please use a different email.");
            return;
        }

        // 邮箱未被使用，继续 OTP 流程
        document.getElementById("emailOTP").style.display = "block";
        let NewEmailName = emailInput;
        let OTPEMAIL = Math.floor(1000 + Math.random() * 9000);

        // 发送 OTP 邮件
        emailjs.send("Web_ASS", "template_ca0ly36", {
            to_email: String(emailInput),
            otp_code: OTPEMAIL
        }).then(response => {
            alert("OTP has been sent to your email, please check it!");
        }).catch(error => {
            console.error("Failed to send OTP, try again", error);
            alert("Failed to send OTP, please try again.");
        });

        // OTP 验证逻辑
        document.getElementById("OTPbutton").addEventListener("click", function () {
            let VerifirdOTP = parseInt(document.getElementById("OTP").value, 10);
            if (VerifirdOTP !== OTPEMAIL) {
                alert("Your OTP is invalid, try again!");
                document.getElementById("emailOTP").style.display = "none";
            } else {
                // 提交表单到后端保存邮箱
                fetch("app/page/saveEmail.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `email=${encodeURIComponent(emailInput)}`
                })
                .then(response => response.text())
                .then(result => {
                    if (result === "success") {
                        alert("Your email has been changed successfully!");
                        document.getElementById("emailOTP").style.display = "none";
                        document.getElementById("layerEM1").style.display = "none";
                        setTimeout(function () {
                            location.reload();
                        }, 500);
                    } else {
                        alert("Your email has been changed successfully!");
                        document.getElementById("emailOTP").style.display = "none";
                        document.getElementById("layerEM1").style.display = "none";
                        setTimeout(function () {
                            location.reload();
                        }, 500);
                    }
                })
                .catch(error => {
                    console.error("Error updating email:", error);
                    alert("Error updating email, please try again.");
                });
            }
        }, { once: true }); // 确保 OTP 按钮事件只绑定一次
    })
    .catch(error => {
        console.error("Error checking email:", error);
        alert("Error checking email, please try again.");
    });
});

document.getElementById("comfirmYesPN").addEventListener("click", function (event) {
    let phonenumberInput = document.getElementById("phonenumberInput").value.trim();
    let phonenumberFormat = /^[0-9]{3}-[0-9]{7}$/;
    let phonenumberFormat2 = /^[0][0-9]{8,9}$/;
    let phonenumberFormat3 = /^\+60\d{9,10}$/;
    if (phonenumberFormat.test(phonenumberInput) || phonenumberFormat2.test(phonenumberInput) || phonenumberFormat3.test(phonenumberInput)) {
        document.getElementById("layerPN1").style.display = "none";
        setTimeout(function () {
            location.reload();
        }, 500);
    } else {
        alert("Invalid phone number format! \n Format: 123-4567890  |  1234567890  |  234567890");
        event.preventDefault();
        return
    }
});

document.getElementById("imageupload").addEventListener("change", function () {
    document.getElementById("uploadimg").submit();
});

document.getElementById("savebutton").addEventListener("click", function () {
    setTimeout(function () {
        location.reload();
    }, 500);
})

document.getElementById("imageupload").addEventListener("change", function () {
    setTimeout(function () {
        location.reload();
    }, 500);
})