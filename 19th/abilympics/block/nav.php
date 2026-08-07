<input type="button" id="burgerbutton" class="button burgerbutton" value="☰ 選單">

<!-- 平板版改用下拉選單 -->
<div class="navselect center">
	<select id="navselect">
		<option value="index.php" <?= ($page??"")=="index"?"selected":"" ?>>首頁</option>
		<option value="report.php" <?= ($page??"")=="report"?"selected":"" ?>>極光預報</option>
		<option value="diary.php" <?= ($page??"")=="diary"?"selected":"" ?>>旅人日記</option>
		<option value="signin.php" <?= ($page??"")=="admin"?"selected":"" ?>>系統管理</option>
	</select>
</div>

<nav id="nav" class="center">
	<a href="index.php" class="<?= ($page??"")=="index"?"active":"" ?>">首頁</a>
	<a href="report.php" class="<?= ($page??"")=="report"?"active":"" ?>">極光預報</a>
	<a href="diary.php" class="<?= ($page??"")=="diary"?"active":"" ?>">旅人日記</a>
	<a href="signin.php" class="<?= ($page??"")=="admin"?"active":"" ?>">系統管理</a>
</nav>
