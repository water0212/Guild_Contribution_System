<style>
    .header {
        background-color: #1a1a1a;
        color: white;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        justify-content: center; /* 標題置中 */
        position: relative;
        text-align: center;
    }
</style>

<div class="header">
    <div class="nav-buttons d-flex flex-wrap justify-content-center gap-2 mb-3">
        <a href="contribution_record.php" class="nav-btn">≡ 貢獻紀錄</a>
        <a href="contribution_table.php" class="nav-btn">≡ 貢獻任務表</a>
        <a href="member.php" class="nav-btn">👥 成員表</a>
    </div>
    <h1><?= htmlspecialchars($title ?? '網站標題') ?></h1>
</div>