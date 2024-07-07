# hi 如果想連進我的網站可以使用此網址https://hiiamchris.ddns.net/

# 這個裡面如果有問題可以用DC: chris0527 跟我說 或 [用此](https://hiiamchris.ddns.net/anther/respond/) 回報

# 程式都可以自己拿去改拿去參考 不需告知(使用條款請參照TOUv2.0.0.pdf)

---

# 嗨這底下為此網頁的架構文件

此網站主要可以分成3個部分

1. 技能競賽
例如:

46th 49th 50th等等(恩對我知道 1要用st 2要用nd 3要用rd 但我不想管XD)

在53之前的分類是
```
XXth/
    junior/ # (if exsited)
        XXnational/
        XXregional/
    senior/
        XXnational/
            TaskX/
            TaskX/
            TaskX/
            TaskX/
        XXregional/
```
如果有國手選拔賽(2階)就會加XXgrandmaster2stage在senior/中

在54th後將進行架構調整成這樣
```
XXth/
    jXXnational/ # (if exsited)
    jXXregional/ # (if exsited)
    XXnational/
        moduleX/
        moduleX/
        moduleX/
        moduleX/
    XXregional/
```

如果有國手選拔賽(2階)就會加XXgrandmaster2stage/

2. 自己的專案

auther/ --> 一些奇怪的小東西

externalcase/ --> 專案

template/ --> 模板等等

3. 首頁檔案

4. 公告/新聞區
   - https://hiiamchris.ddns.net/news.html

### 在各檔案內會有 about/ 內可以看到該專案的詳細資料

---