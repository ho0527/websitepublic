/**
 * JavaScript - ASCII Art Editor
 *
 * Your task is to implement all methods marked with @todo. You must not change any other method.
 * You may add as many methods to the ASCIIArtEditor class as you want.
 */


/**
 * Constructor function to create a new ASCIIArtEditor
 * @constructor
 */
var ASCIIArtEditor = function () {
    /**
     * When transforming images this property should be used as line
     * separator for all operations
     * @type {string}
     */
    this.lineSeperator = '\n';
};


/**
 * This function takes an arbitrary ASCII Art string as input and must
 * return a mirrored version of the given image.
 *
 * It should be possible to choose the mirror-axis with the second argument.
 *
 * The function should use the configured lineSeparator property on
 * the ASCIIArtEditor object.
 *
 * Example on 'x' axis:
 *   Input:                 Output:
 *     # --····-- $           # --====-- $
 *     #  +    -  $           #  +    -  $
 *     # --====-- $           # --····-- $
 *
 * Example on 'y' axis:
 *   Input:                 Output:
 *     # --····-- $           $ --····-- #
 *     #  +    -  $           $  -    +  #
 *     # --====-- $           $ --====-- #
 *
 * @param {string} image - the source image
 * @param {'x'|'y'} [axis='y'] - the axis to be used for the mirror operation, defaults to 'y'
 * @return string - the mirrored input image
 *
 * @throws Error - If an invalid axis was provided
 *
 * @todo
 */
ASCIIArtEditor.prototype.mirror=function(image,axis){
    // <---- Implement this method
    if(axis!="x"&&axis!="y"){
        throw new Error("Invalid axis")
    }

    let lines=image.split(this.lineSeperator)
    let result=[]

    if(axis=="x"){
        let len=lines.length
        for(let i=0;i<len;i=i+1){
            result[i]=lines[len-i-1]
        }
    }else{
        for(let i=0;i<lines.length;i=i+1){
            let line=lines[i]
            let reversed=""
            for(let j=0;j<line.length;j=j+1){
                reversed=line.charAt(j)+reversed
            }
            result.push(reversed)
        }
    }

    return result.join(this.lineSeperator)
}


/**
 * Takes any SQUARE ASCII image and must rotate the image by the
 * given angle (only multiple of 90 allowed).
 *
 * The rotation should always be clockwise.
 *
 * Example:
 *   Input:    90deg:    180deg:    270deg:    360deg:
 *     #-*       $-#       *-$        ***         #-*
 *     --*       ---       *--        ---         --*
 *     $-*       ***       *-#        #-$         $-*
 *
 * @param {string} image
 * @param {number} angle, has to be one of 0, 90, 180, 270, 360
 * @return string
 *
 * @throws Error - If an invalid angle was provided
 *
 * @todo
 */
ASCIIArtEditor.prototype.rotate=function(image,angle){
    // <---- Implement this method
    if(![0,90,180,270,360].includes(angle)){
        throw new Error("Invalid angle")
    }

    let lines=image.split(this.lineSeperator)
    let size=lines.length
    let result=[]

    for(let i=0;i<size;i=i+1){
        result.push("")
    }

    if(angle==0||angle==360){
        return image
    }

    if(angle==90){
        for(let i=0;i<size;i=i+1){
            for(let j=size-1;j>=0;j=j-1){
                result[i]=result[i]+lines[j].charAt(i)
            }
        }
    }

    if(angle==180){
        for(let i=size-1;i>=0;i=i-1){
            let reversed=""
            for(let j=lines[i].length-1;j>=0;j=j-1){
                reversed=reversed+lines[i].charAt(j)
            }
            result[size-1-i]=reversed
        }
    }

    if(angle==270){
        for(let i=0;i<size;i=i+1){
            for(let j=0;j<size;j=j+1){
                result[i]=result[i]+lines[j].charAt(size-1-i)
            }
        }
    }

    return result.join(this.lineSeperator)
}