/**
 * Test File
 *  You can not edit this file!!!!!
 */

//console: "boolean"
console.log(getType(true));

//console: "number"
console.log(getType(123));

//console: "bigint"
console.log(getType(123n));

//console: "string"
console.log(getType("abc"));

//console: "array"
console.log(getType([1, 2, 3, 4]));

//console: "object"
console.log(getType({a: 1, b: 2}));

//console: "regexp"
console.log(getType(/d/));

//console: "null"
console.log(getType(null));

//console: "undefined"
console.log(getType(undefined));
