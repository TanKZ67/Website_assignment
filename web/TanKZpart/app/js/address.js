document.getElementById("newaddres").addEventListener("click", function () {
    document.getElementById("layerNA1").style.display = "block";
})

document.getElementById("savebutton").addEventListener("click", function () {
    document.getElementById("layerNA1").style.display = "none";
    setTimeout(function () {
        location.reload();
    }, 500);
})


document.getElementById("cancelbutton2").addEventListener("click", function () {
    document.getElementById("layerNA1").style.display = "none";

})



// 使用 .edit 选择器来选取所有具有 edit 类的按钮
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".edit").forEach(button => {
        button.addEventListener("click", function () {
            // 获取按钮的 data-id 属性，作为地址 ID
            const addressId = button.getAttribute("data-addid");

            // 打开编辑层
            document.getElementById("layerA1").style.display = "block";

        });
    });
})

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".delete").forEach(button => {
        button.addEventListener("click", function () {
            // 获取按钮的 data-id 属性，作为地址 ID
            const addressId = button.getAttribute("data-addid");

            // 打开编辑层
            document.getElementById("layerA1").style.display = "block";

        });
    });
})
