<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <title>点字滑出选项</title>
  <style>
    #options {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease;
    }

    #options.show {
      max-height: 100px; /* 根据内容调整 */
    }
  </style>
</head>
<body>

<!-- 点击的文字 -->
<a onclick="toggleOptions()" style="cursor: pointer; color: blue;">点我</a>

<!-- 滑出/隐藏的选项 -->
<div id="options">
  <button>选项一</button><br>
  <button>选项二</button>
</div>

<script>
  function toggleOptions() {
    const opt = document.getElementById("options");
    opt.classList.toggle("show");
  }
</script>

</body>
</html>
