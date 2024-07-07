
document.getElementById("newform").onclick=function(){
    lightbox(null,"lightbox",function(){
        return `
            <form>
                問卷名稱: <input type="text" class="input" name="title" placeholder="問卷名稱"><br><br>
                問卷題數: <input type="text" class="input" name="count" placeholder="問卷題數"><br><br>
                問卷分頁題數: <input type="text" class="input inputshort" name="pagelen" value="-1">
                <input type="button" class="stbutton outline light" id="cancel" value="取消">
                <input type="submit" class="stbutton outline light" name="submit" value="確定">
            </form>
        `
    },"cancel")
}

startmacossection()