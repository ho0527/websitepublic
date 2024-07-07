let html=`
    <form>
        問卷名稱: <input type="text" class="input" name="title" placeholder="問卷名稱"><br><br>
        問卷題數: <input type="text" class="input" name="count" placeholder="問卷題數"><br><br>
        問卷分頁題數: <input type="text" class="inputshort" name="pagelen" placeholder="分頁題數">
        <input type="button" class="button" onclick="location.reload()" value="取消">
        <input type="submit" class="button" name="submit" value="確定">
    </form>
`

lightbox("newform","lightbox",html)
startmacossection()