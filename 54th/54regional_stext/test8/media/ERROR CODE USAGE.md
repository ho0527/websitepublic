
### usage
- 無
## 100 Continue
`HTTP 100 Continue` 訊息狀態回應碼表示，目前為止的一切完好，用戶端應該繼續完成請求，或當請求已經完成的狀態下應忽略此訊息。

若要使伺服器確認請求標頭，用戶端必須在最初請求的標頭中，傳送Expect (en-US): 100-continue ，並且在傳送主體前，接收 100 Continue 狀態碼。

### usage
- 無

## 101 Switching Protocols
`HTTP 101 Switching Protocols` 回應碼表示伺服器正在切換到用戶端在請求標頭Upgrade 中所要求的通訊協定。

伺服端將在回應中包含 Upgrade 標頭以表明其所切換的通訊協定。此過程在文章 Protocol upgrade mechanism 中有更加詳細的描述。

### usage
- 無

## ~~102 Processing~~

`HTTP 102 Processing` 資訊狀態回應代碼向客戶端表示已收到完整請求且伺服器正在處理該請求。

僅當伺服器預計請求需要很長時間時才會發送此狀態代碼。它告訴客戶端您的請求尚未終止。

***請注意：此狀態代碼已棄用，不應再發送。客戶可能仍然接受它，但只是忽略它們。***

### usage
- 無

## 103 Early Hints

`HTTP 103 Early Hints`可能由伺服器在準備回應時傳送，其中包含有關伺服器期望最終回應將連結的網站和資源的提示。這使得瀏覽器甚至可以在伺服器準備並發送最終回應之前 預先連接到網站或開始預先載入資源。

早期提示回應主要用於與Link標頭一起使用，標頭指示要載入的資源。它還可能包含Content-Security-Policy在處理早期提示時強制執行的標頭。

伺服器可能會發送多個103回應，例如，在重定向之後。瀏覽器僅處理第一個早期提示回應，如果請求導致跨來源重新導向，則必須丟棄該回應。來自早期提示的預先載入資源有效地預先附加到 的Documenthead 元素，然後是最終回應中載入的資源。

***注意：出於相容性原因，建議103 Early Hints僅透過 HTTP/2 或更高版本發送 HTTP 回應，除非已知用戶端能夠正確處理資訊回應。***

***由於這個原因，大多數瀏覽器限制對 HTTP/2 或更高版本的支援。請參閱下面的瀏覽器相容性。***

### usage
- 無

## 200 OK
`HTTP 200 OK`狀態碼表明請求成功。200 回應預設上是緩存的（cacheable）。

所謂「成功」的定義，取決於 HTTP 的請求方法：
- GET：資源已取得，並傳送到訊息內文（message body）。
- HEAD (en-US)：整個標已經放在訊息內文了。
- POST (en-US): 描述動作結果的資源已經傳送到訊息內文。
- TRACE (en-US)：訊息內文包含了請求訊息，伺服器也接受了請求。

PUT 或 DELETE 的成功訊息，通常不是 200 OK，而是 204 No Content（或著在資源首次上傳時，201 Created）。

### usage
- 無

## 201 Created

`HTTP 201 Created` 成功狀態碼表示請求成功且有一個新的資源已經依據需要而被建立。實際上，在此回應傳送前，新資源就已被建立，且其內容在訊息的主體中傳回，其位置為請求的 URL 或是 Location 標頭的內容。

此狀態碼通常用於 POST 請求的回應中。

### usage
- 無

## 202 Accepted

`HTTP 202 Accepted` 成功狀態碼表示此請求已經被接受但尚未處理。

此狀態為非承諾性，代表 HTTP 無法在之後傳送一個非同步的回應告知請求的處理結果。最初目的為外部程序或其他伺服器處理請求的情況，或用於批次處理中。

### usage
- 無

## 203 Non-Authoritative Information
`HTTP 203 Non-Authoritative Information` 狀態碼表明請求成功，但是與原始伺服器的 200 (OK) 回應相比，隨附的酬載已被具轉換功能的 代理伺服器 所修改。

203 回應相似於 Warning 標頭的 214 Transformation Applied，但後者的額外的優點在於可以套用到任何狀態碼的回應中。

### usage
- 無

## 204 No Content
`HTTP 204 No Content` 成功狀態碼表明請求成功，但客戶端不需要更新目前的頁面。204 回應預設是可被快取的，此類回應中會包含 ETag 標頭。

回傳 204 的常見情況是作為 PUT 請求的回應，更新一個資源且沒有更動目前顯示給使用者的頁面內容。若是有資源被建立，201 Created 則應該被回傳。而若頁面應該更新為新的頁面，則應使用 200 。

### usage
- 無

## 205 Reset Content
`HTTP 205 Reset Content` 狀態碼用來通知客戶端重置文件視圖，例如：清除表單內容、重置畫布狀態或刷新使用者界面。

### usage
- 無

## 206 Partial Content

`HTTP 206 Partial Content` 成功狀態碼表明請求成功，且主體包含在請求標頭 Range 中所指定的資料區間。

若只包含一個區間，則整個回應的 Content-Type 將會被設為該文件的類型 ，且會包含一個 Content-Range 標頭。

若有多個區間，則整個回應的 Content-Type 會被設為 multipart/byteranges ，且每個分段會對應一個區間，並有 Content-Range 及 Content-Type 描述各個區間。

### usage
- 無

## 207 Multi-Status

***注意：傳回資源集合的功能是WebDAV協定的一部分（它可以由存取 WebDAV 伺服器的 Web 應用程式接收）。造訪網頁的瀏覽器永遠不會遇到此狀態代碼。***

`HTTP 207 Multi-Status` 回應代碼表示可能存在混合回應。

回應正文是一個text/xml具有根元素application/xml的 HTTP 實體multistatus。 XML 正文將列出所有單獨的回應代碼。

### usage
- 無

## 208 Already Reported

***注意：將資源綁定到多個路徑的功能是WebDAV協定的擴充（它可以由存取 WebDAV 伺服器的 Web 應用程式接收）。造訪網頁的瀏覽器永遠不會遇到此狀態代碼。***

`HTTP208 Already Reported`回應碼用在207( 207 Multi-Status) 回應中以節省空間並避免衝突。如果使用不同的路徑多次要求相同資源（例如作為集合的一部分），則僅使用 報告第一個資源200。所有其他綁定的回應將使用此208狀態代碼進行報告，因此不會產生衝突並且回應會保持較短。

### usage
- 無

## 226 IM Used

***注意：瀏覽器不支援HTTP增量編碼。此狀態代碼由特定用戶端使用的自訂伺服器傳回。***

在增量編碼的上下文中，`HTTP 226 IM Used`狀態代碼由伺服器設置，以指示它正在返回其收到的請求的增量。GET

透過增量編碼，伺服器回應相對於給定基礎文件（而不是當前文件）的GET差異（稱為增量）的請求。用戶端使用A-IM:HTTP 標頭來指示要使用哪種差異演算法，並使用該If-None-Match:標頭來提示伺服器其獲得的最新版本。伺服器產生一個增量，在 HTTP 回應中將其發送回226，其中包含狀態代碼並包含IM:（使用所使用的演算法的名稱）和Delta-Base:（與ETag增量相關的基本文件的匹配）HTTP 標頭。

IM 代表實例操作，該術語用於描述產生增量的演算法。

### usage
- 無

## 300 Multiple Choices

`HTTP 300 Multiple Choices` 重定向回應碼代表該請求具有超過一種可能的回應。用戶代理或使用者應該從中挑選一個。由於不存在標準化的選擇回應方式，此回應碼非常少被使用。

若是伺服端有偏好的選擇，則應該產生 Location 標頭。

### usage
- 無

## 301 Moved Permanently

`HTTP 301 Moved Permanently` 重新導向狀態代碼代表所請求的資源已經被明確移動到 Location (en-US) 標頭所指示的 URL。瀏覽器會重新導向到此頁面，而搜尋引擎則會更新該資源的連結。用 SEO 的話來說，就是連結養分（link-juice）把你送到了新的 URL 去。

儘管規範要求當執行重新導向時，請求方法（以及主體）不應該被更動，但並非所有的用戶代理皆遵循它——你依然可以找到具有此類漏洞的軟體。因此，推薦只使用 301 回應碼作為 GET 或 HEAD (en-US) 方法的回應，另外使用 308 Permanent Redirect (en-US) 作為 POST (en-US) 方法的替代，因為請求方法的更動在此狀態中是被明確禁止的。

### usage
- 無

## 302 Found

`HTTP 302 Found` 重定向狀態回應代碼表示請求的資源已暫時移至Location標頭給出的 URL。瀏覽器重定向到此頁面，但搜尋引擎不會更新其資源連結（在「SEO 術語」中，據說「連結汁」不會發送到新 URL）。

即使規範要求在執行重定向時不得更改方法（和主體），但並非所有用戶代理都符合此處 - 您仍然可以在那裡找到此類存在缺陷的軟體。因此，建議 302僅將程式碼設定為GET或 HEAD方法的回應並改為使用307 Temporary Redirect ，因為在這種情況下明確禁止方法變更。

如果您希望將使用的方法變更為GET，請改用 303 See Other。當您想要對PUT不是上傳資源的方法 做出回應而是確認訊息（例如：「您已成功上傳 XYZ」）時，這非常有用。

### usage
- 無

## 303 See Other

`HTTP 303 See Other`重定向狀態回應代碼指示重定向不連結到請求的資源本身，而是連結到另一個頁面（例如確認頁面，現實世界物件的表示 - 請參閱HTTP range-14 —或上傳進度頁面）。此回應代碼通常會作為 PUT或的結果發回POST。用於顯示此重定向頁面的方法始終是GET。

### usage
- 無

## 304 Not Modified

`HTTP 304 Not Modified` 用戶端重新導向回應碼表示不需要重傳所要求的資源。當請求是條件請求GET或HEAD帶有If-None-Match或If-Modified-Since標頭的請求且條件計算結果為 false 時，將發送此回應代碼。它是對快取資源的隱式重定向，如果條件評估為 true， 則會產生回應。200 OK

回應不得包含正文，且必須包含在等效回應中傳送的標頭： `Cache-Control`, `Content-Location`, `Date`, `ETag`, `Expires`, and `Vary` 。 200 ok

### usage
- 無

## !? 305 Use Proxy

### usage
- 無

## !? 306 Switch Proxy

### usage
- 無

## 307 暫時重定向
`HTTP 307 Temporary Redirect`重新導向狀態回應代碼表示所要求的資源已暫時移至Location標頭所給的 URL。

原始請求的方法和正文被重複用來執行重定向的請求。如果您希望將使用的方法變更為 GET，請改用303 See Other。當您想要對PUT不是上傳資源的方法 給出答案，而是一條確認訊息（例如「您已成功上傳 XYZ」）時，這非常有用。

307和 302 之間的唯一區別302是 307保證在發出重定向請求時方法和主體不會更改。對於302，一些舊客戶端錯誤地將方法變更為：使用非 方法GET的行為，然後在 Web 上是不可預測的，而使用 的行為 是可預測的。對於請求，它們的行為是相同的。

### usage
- 無

## 400 Bad Request

`HTTP 400 Bad Request`回應狀態碼表示由於用戶端的錯誤（例如請求語法的格式錯誤、請求訊息框架無效或欺騙性的請求路由），伺服器無法或不會處理請求。

**警告： 用戶端不應原封不動的重複此要求。**

### usage
- 無

## 401 Unauthorized

`HTTP 401 Unauthorized`回應狀態代碼指示用戶端請求尚未完成，因為它缺少所請求資源的有效身份驗證憑證。

此狀態代碼與 HTTP 回應標頭一起傳送WWW-Authenticate，其中包含有關用戶端在提示使用者輸入驗證憑證後如何再次請求資源的資訊。

此狀態代碼與狀態代碼類似403 Forbidden，不同之處在於，在導致此狀態代碼的情況下，使用者身份驗證可以允許存取資源。

### usage
- 無

## !? 402 Payment Required

`HTTP 402 Payment Required`是一種非標準回應狀態代碼，保留供將來使用。建立此狀態代碼是為了啟用數位現金或（微型）支付系統，並指示在客戶付款之前所要求的內容不可用。

有時，此狀態代碼表示在客戶端付款之前無法處理請求。然而，不存在標準的使用約定，不同的實體在不同的上下文中使用它。

### usage
- 無

## 403 Forbidden

`HTTP 403 Forbidden`客戶端錯誤狀態碼表示伺服器理解該請求但拒絕核准。

該狀態碼與 HTTP 401 (en-US) 類似，但重新身分驗證不能提供幫助。 存取被永久性禁止，且與應用程式邏輯有關，如資源的訪問權限不足。

### usage
- 無

## 404 Not Found

`HTTP 404 Not Found` 用戶端錯誤回應碼，表明了伺服器找不到請求的資源。引發 404 頁面的連結，通常被稱作斷連或死連（broken or dead link）、並可以導到失效連結（link rot）頁面。

404 狀態碼並沒有表明資源是暫時不見、還是永遠不見。如果資源是永遠不見，就應該用 410 Gone 而不是 404。

### usage
- 無

## 405 Method Not Allowed

`HTTP 405 Method Not Allowed`回應狀態代碼表示伺服器知道請求方法，但目標資源不支援此方法。

伺服器必須Allow在 405 狀態代碼回應中產生標頭欄位。此欄位必須包含目標資源目前支援的方法清單。

### usage
- 無

## 406 Not Acceptable

`HTTP 406 Not Acceptable` 用戶端錯誤回應代碼指示伺服器無法產生與請求的主動內容協商標頭 中定義的可接受值清單相符的回應，且伺服器不願意提供預設表示形式。

主動內容協商標頭包括：
- Accept
- Accept-Encoding
- Accept-Language
實際上，這個錯誤很少被使用。伺服器不會使用此錯誤代碼進行回應（這對於最終用戶來說是神秘的且難以修復），而是忽略相關標頭並向用戶提供實際頁面。假設即使用戶不會完全滿意，他們也會更喜歡這個而不是錯誤代碼。

如果伺服器傳回此類錯誤狀態，則訊息正文應包含資源的可用表示形式的列表，允許使用者在其中進行選擇。

### usage
- 無

## 407 需要代理驗證

`HTTP 407 Proxy Authentication Required`用戶端錯誤狀態回應代碼表示請求尚未套用，因為它缺少位於瀏覽器和可以存取所請求資源的伺服器之間的 代理伺服器的有效身份驗證憑證。

Proxy-Authenticate此狀態與包含有關如何正確授權的資訊的標頭 一起發送。

### usage
- 無

## 408 Request Timeout
`HTTP 408 Request Timeout`回應狀態代碼表示伺服器想要關閉此未使用的連線。即使客戶端之前沒有發出任何請求，某些伺服器也會在空閒連線上發送它。

伺服器應該Connection在回應中發送“close”標頭字段，因為408這意味著伺服器已決定關閉連接而不是繼續等待。

由於某些瀏覽器（例如​​ Chrome、Firefox 27+ 和 IE9）使用 HTTP 預連接機制來加速衝浪，因此此回應的使用更加頻繁。

***注意：有些伺服器只是關閉連線而不發送此訊息。***

### usage
- 無

## 409 Conflict

`HTTP 409 Conflict`回應狀態碼表示請求與目標資源的目前狀態發生衝突。

在回應請求時最有可能發生衝突PUT。例如，上傳比伺服器上現有檔案舊的檔案時，您可能會收到 409 回應，導致版本控制衝突。

### usage
- 無

## 410 Gone

`HTTP 410 Gone`用戶端錯誤回應代碼指示來源伺服器不再提供對目標資源的訪問，並且這種情況可能是永久性的。

如果您不知道這種情況是暫時的還是永久的，則應使用404狀態代碼。

### usage
- 無

## 411 Length Required

`HTTP 411 Length Required`用戶端錯誤回應代碼表示伺服器拒絕接受沒有定義標頭的請求 Content-Length。

***注意：根據規範，當以一系列區塊發送資料時， Content-Length頭被省略，並且在每個區塊的開頭，您需要以十六進位格式添加當前區塊的長度。請參閱 Transfer-Encoding了解更多詳情。***

### usage
- 無

## 412 Precondition Failed

`HTTP 412 Precondition Failed` 用戶端錯誤回應代碼表示對目標資源的存取已被拒絕。當不滿足或標頭定義的條件時， 除了GET或HEAD之外的方法上的條件請求時，會發生這種情況。在這種情況下，無法發出請求（通常是上傳或修改資源），並且會傳回此錯誤回應。

### usage
- 無

## 413 Content Too Large

`HTTP 413 Content Too Large`回應狀態碼表示請求實體大於伺服器定義的限制；伺服器可能會關閉連線或傳回Retry-After標頭欄位。

在 RFC 9110 之前，狀態的回應短語是Payload Too Large。這個名字至今仍被廣泛使用。

### usage
- 無

## 414 URI Too Long

`HTTP 414 URI Too Long`回應狀態碼表示客戶端請求的 URI 比伺服器願意解釋的長度長。

在一些罕見的情況下可能會發生這種情況：
- 當客戶端錯誤地將POST請求轉換為 GET具有長查詢資訊的請求時，
- 當客戶端陷入重定向循環時（例如，指向其自身後綴的重定向 URI 前綴），
- 或當伺服器受到試圖利用潛在安全漏洞的客戶端攻擊時。

### usage
- 無

## 415 Unsupported Media Type

`HTTP 415 Unsupported Media Type`客戶端錯誤回應代碼表示伺服器拒絕接受請求，因為負載格式不受支援。

格式問題可能是由於請求指示 Content-Type或造成的Content-Encoding，或直接檢查資料的結果。

### usage
- 無

## 416 Range Not Satisfiable

`HTTP 416 Range Not Satisfiable`錯誤回應代碼表示伺服器無法為請求的範圍提供服務。最可能的原因是文件不包含此類範圍，或Range標頭值雖然語法正確，但沒有意義。

回應416訊息包含Content-Range指示不滿足範圍的 a（即 a '*'），後面接著 a'/'和資源的當前長度。例如Content-Range: bytes */12777

面對此錯誤，瀏覽器通常要么中止操作（例如，下載將被視為不可恢復），要麼再次請求整個文件。

### usage
- 無

## 417 Expectation Failed

`HTTP 417 Expectation Failed` 客戶端錯誤回應代碼表示無法滿足請求Expect標頭中給予的期望。

請參閱Expect標題以了解更多詳細資訊。

### usage
- 無

## 418 I'm a teapot
`HTTP 418 I'm a teapot`客戶端錯誤回應代碼表示伺服器拒絕沖泡咖啡，因為它永遠是一個茶壺。暫時沒有咖啡的組合咖啡/茶壺應該返回 503。此錯誤引用了 1998 年和 2014 年愚人節笑話中定義的超文本咖啡壺控制協議。

一些網站使用此回應來處理他們不希望處理的請求，例如自動查詢。

### usage
- 無

## 421 Misdirected Request
`HTTP 421 Misdirected Request`客戶端錯誤回應代碼表示請求被導向到無法產生回應的伺服器。如果重複使用連線或選擇替代服務，這可能是可能的。

### usage
- 無

## 422 無法處理的內容

`HTTP 422 Unprocessable Content`回應狀態代碼表示伺服器了解請求實體的內容類型，並且請求實體的語法正確，但無法處理所包含的指令。

**警告：客戶端不應在未經修改的情況下重複此請求。**

### usage
- 無

## 423 鎖定

`HTTP 423 Locked`錯誤回應代碼表示暫定目標的資源已被鎖定，這表示無法存取它。其內容應包含一些WebDAV XML 格式的資訊。

***注意：鎖定資源的功能特定於某些WebDAV伺服器。瀏覽器造訪網頁永遠不會遇到這個狀態碼；在發生錯誤的情況下，他們會將其作為通用400狀態代碼進行處理。***

### usage
- 無

## 424 Failed Dependency

`HTTP 424 Failed Dependency`用戶端錯誤回應代碼表示無法對資源執行該方法，因為請求的操作依賴於另一個操作，且該操作失敗。

常規 Web 伺服器通常不會傳回此狀態代碼。但其他一些協定（例如WebDAV）可以傳回它。例如，在WebDAV中，如果PROPPATCH發出請求，並且一個命令失敗，則所有其他命令也會自動失敗，並顯示424 Failed Dependency.

### usage
- 無

## 425 Too Early

`HTTP 425 Too Early`回應狀態碼表示伺服器不願意冒險處理可能重播的請求，這可能會造成重播攻擊。

### usage
- 無

## 426 Upgrade Required

`HTTP 426 Upgrade Required`客戶端錯誤回應代碼表示伺服器拒絕使用目前協定執行請求，但在客戶端升級到不同協定後可能願意執行該請求。

伺服器發送Upgrade帶有此回應的標頭以指示所需的協定。

### usage
- 無

## 428 Precondition Required

`HTTP 428 Precondition Required`回應狀態碼表示伺服器要求請求是有條件的。

例如，通常，這意味著缺少If-Match所需的前提條件標頭。

當前提條件標頭與伺服器端狀態不符時，回應應為 `412 Precondition Failed`。

### usage
- 無

## 429 Too Many Requests

`HTTP 429 Too Many Requests` 回應狀態代碼指示使用者在給定時間內發送了太多請求（「速率限制」）。

Retry-After此回應中可能包含一個標頭，指示在發出新請求之前需要等待多長時間。

### usage
- 無

## 431 請求標頭欄位太大

`HTTP 431 Request Header Fields Too Large`回應狀態碼表示伺服器拒絕處理請求，因為請求的 HTTP標頭太長。減少請求標頭的大小後 可以重新提交請求。

當請求頭的總大小太大，或單一頭字段太大時， 可以使用431 。為了幫助那些遇到此錯誤的人，請在回應正文中指出這兩個錯誤中的哪一個是問題所在 - 理想情況下，還包括哪些標頭太大。這讓用戶可以嘗試解決問題，例如清除 cookie。

如果出現以下情況，伺服器通常會產生此狀態：
- 網址Referer太長
- 請求中發送的 Cookie 過多

### usage
- 無

## 451 Unavailable For Legal Reasons

`HTTP 451 Unavailable For Legal Reasons`用戶端錯誤回應代碼表示使用者請求的資源由於法律原因而不可用，例如已發出法律訴訟的網頁。

### usage
- 無

## 500 Internal Server Error

`HTTP 500 Internal Server Error`伺服器錯誤回應代碼表示伺服器遇到意外情況，導致其無法完成請求。

此錯誤回應是通用的“包羅萬象”回應。通常，這表示伺服器無法找到更好的 5xx 錯誤代碼來回應。有時，伺服器管理員會記錄錯誤回應（例如 500 狀態代碼）以及有關請求的更多詳細信息，以防止將來再次發生錯誤。

### usage
- 無

## 501 Not Implemented

`HTTP 501 Not Implemented`伺服器錯誤回應代碼表示伺服器不支援完成請求所需的功能。

此狀態還可以發送Retry-After標頭，告訴請求者何時檢查以查看屆時是否支援該功能。

501當伺服器無法識別請求方法並且無法支援任何資源時，是適當的回應。伺服器需要支援的唯一方法（因此不得返回501）是GET和HEAD。

如果伺服器確實識別該方法，但故意不支援它，則適當的回應是`405 Method Not Allowed`。

筆記：
- 501 錯誤無法修復，但需要您嘗試存取的 Web 伺服器進行修復。
- 預設情況下，501 回應是可緩存的；也就是說，除非快取標頭另有指示。

### usage
- 無

## 502 Bad Gateway

`HTTP 502 Bad Gateway`伺服器錯誤回應代碼表示伺服器在充當網關或代理程式時從上游伺服器接收無效回應。

注意：網關可能指的是網路中的不同事物，502錯誤通常無法修復，但需要 Web 伺服器或您嘗試存取的代理進行修復。

### usage
- 無

## 503 Service Unavailable

`HTTP 503 Service Unavailable` 伺服器錯誤回應代碼表示伺服器尚未準備好處理請求。

常見原因是伺服器因維護而停機或過載。此回應應用於臨時情況，且Retry-AfterHTTP 標頭應（如果可能）包含服務恢復的估計時間。

***注意：應與此回應一起發送解釋問題的使用者友善頁面。***

應注意隨此回應一起發送的與快取相關的標頭，因為 503 狀態通常是臨時條件，通常不應快取回應。

### usage
- 無

## 504 504 Gateway Timeout

`HTTP 504 Gateway Timeout`伺服器錯誤回應代碼表示伺服器在充當網關或代理程式時，沒有及時從上游伺服器獲得完成請求所需的回應。

***注意：網關可能指的是網路中的不同事物，而504 錯誤通常無法修復，但需要 Web 伺服器或您嘗試存取的代理進行修復。***

### usage
- 無

## 505 HTTP Version Not Supported

`HTTP 505 HTTP Version Not Supported`回應狀態碼表示伺服器不支援請求中使用的 HTTP 版本。

### usage
- 無

## 506 Variant Also Negotiates

`HTTP 506 Variant Also Negotiates`回應狀態代碼可以在透明內容協商的上下文中給出（請參閱RFC 2295）。該協定使客戶端能夠檢索給定資源的最佳變體，其中伺服器支援多個變體。

狀態代碼Variant Also Negotiates指示內部伺服器配置錯誤，其中所選變體本身配置為參與內容協商，因此不是正確的協商端點。

### usage
- 無

## 507 Insufficient Storage

`HTTP 507 Insufficient Storage`回應狀態代碼可以在 Web 分散式創作和版本控制 (WebDAV) 協定的上下文中給出（請參閱RFC 4918）。

它指示無法執行某個方法，因為伺服器無法儲存成功完成請求所需的表示。

### usage
- 無

## 508 Loop Detected

`HTTP 508 Loop Detected` 回應狀態代碼可以在Web分散式創作和版本控制（WebDAV）協定的上下文中給出。

它表示伺服器終止了操作，因為在處理「Depth: infinity」的請求時遇到無限循環。此狀態表示整個操作失敗。

### usage
- 無

## 510 Not Extended

`HTTP 510 Not Extended`回應狀態代碼在RFC 2774 中定義的 HTTP 擴充框架的上下文中傳送。 

在該規範中，客戶端可以發送包含擴展聲明的請求，該擴展聲明描述了要使用的擴展。如果伺服器收到此類請求，但該請求不支援任何描述的擴展，則伺服器會使用 510 狀態代碼進行回應。

### usage
- 無

## 511 Network Authentication Required

`HTTP 511 Network Authentication Required`回應狀態代碼表示客戶端需要進行身份驗證才能獲得網路存取權限。

此狀態不是由來源伺服器產生的，而是透過攔截控制網路存取的代理程式產生的。

網路營運商有時在授予存取權限之前需要進行一些身份驗證、接受條款或其他使用者互動（例如在網咖或機場）。他們經常使用媒體存取控制 (MAC) 位址來識別尚未這樣做的用戶端。

### usage
- 無