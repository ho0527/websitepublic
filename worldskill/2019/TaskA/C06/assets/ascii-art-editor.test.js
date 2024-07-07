// testing function
function test(testNr, testDescr, input, expect, funcName, funcArg, lineSep = '\n') {
    var editor = new ASCIIArtEditor();
    editor.lineSeperator = lineSep;
    try {
        var output = editor[funcName](input, funcArg); // execute method
    } catch (e) {
        output = "exception";
    }
    if(output != null && output != undefined){
        var comparision = output.localeCompare(expect) ? "&#x2718; does NOT match" : "&#x2714; does match"; // 0 = equals
    }else {
        var comparision = "&#x2718; does NOT match";
    }
    var testrun =
        `<div>
            <h1>Test ${testNr}: ${testDescr}</h1>
            <div>
                <b>Input:</b> <div id="test-input"><pre>${input}</pre></div>
            </div>
            <div>
                <b>Output:</b> <div id="test-output"><pre>${output}</pre></div>
                <b>Expected Output:</b> <div id="test-expected"><pre>${expect}</pre></div>
                <div id="test-comparision" data-value="${comparision}">${comparision}</div>
            </div>
        </div>`;
    document.getElementById("testrun-results").innerHTML += testrun;
}

// testcases:
// test 1
var input = [
    '*    +    °',
    '<=========>',
    '°    -    *',
].join('\n');

var expected = [
    '°    +    *',
    '>=========<',
    '*    -    °',
].join('\n');

test(1, "should mirror the image vertically", input, expected, "mirror", "y");


// test 2
var input = [
    '*    +    °',
    '<=========>',
    '°    -    *',
].join('\n');

var expected = [
    '°    -    *',
    '<=========>',
    '*    +    °',
].join('\n');

test(2, "should mirror the image horizontally", input, expected, "mirror", "x");


// test 3
test(3, "should throw an exception on invalid mirror axis", '===>', 'exception', "mirror", "a");


// test 4
var input = [
    '*    +    °',
    '<=========>',
    '°    -    *',
].join('\r\n');

var expected = [
    '°    -    *',
    '<=========>',
    '*    +    °',
].join('\r\n');

test(4, "should mirror the image with custom line separator", input, expected, "mirror", "x", '\r\n');


// test 5
var input = [
    '*----',
    '     ',
    '==·==',
    '     ',
    '----*',
].join('\n');

var expected = [
    '- = *',
    '- = -',
    '- · -',
    '- = -',
    '* = -',
].join('\n');

test(5, "should rotate the image 90deg", input, expected, "rotate", "90");


// test 6
var input = [
    '**-++',
    '     ',
    '==###',
    '     ',
    '++-**',
].join('\n');

var expected = [
    '**-++',
    '     ',
    '###==',
    '     ',
    '++-**',
].join('\n');

test(6, "should rotate the image 180deg", input, expected, "rotate", "180");


// test 7
var input = [
    '**-++',
    '     ',
    '==###',
    '     ',
    '++-**',
].join('\n');

var expected = [
    '+ # *',
    '+ # *',
    '- # -',
    '* = +',
    '* = +',
].join('\n');

test(7, "should rotate the image 270deg", input, expected, "rotate", "270");


// test 8
var input = [
    '**-++',
    '     ',
    '==###',
    '     ',
    '++-**',
].join('\n');

var expected = [
    '**-++',
    '     ',
    '==###',
    '     ',
    '++-**',
].join('\n');

test('8a', "should rotate the image 0 | 360deg", input, expected, "rotate", "0");
test('8b', "should rotate the image 0 | 360deg", input, expected, "rotate", "360");


// test 9
test('9a', "should throw an exception on invalid angle", '===>', 'exception', "rotate", "53");
test('9b', "should throw an exception on invalid angle", '===>', 'exception', "rotate", "-53");
test('9c', "should throw an exception on invalid angle", '===>', 'exception', "rotate", "371");
