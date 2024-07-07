let data = {
    str: "",
    num: 10000,
    bool: false,
    arr: [
        {
            str: "",
            num: 10000,
            bool: false,
            foo: () => {
                return true;
            }
        },
        {
            str: "",
            num: 10000,
            bool: false,
            foo: () => {
                return true;
            }
        }
    ],
    foo: () => {
        return true;
    },
    children: {
        c1: {
            str: "",
            num: 10000,
            bool: false,
            foo: () => {
                return true;
            },
            children: {
                c1: {
                    str: "",
                    num: 10000,
                    bool: false,
                    foo: () => {
                        return true;
                    }
                }
            }
        },
    }
};

let process_data = deepClone(data);

let result_1 = process_data !== data && process_data?.foo() && process_data?.arr[1]?.foo() && process_data?.children?.c1?.children?.c1?.foo();


let data2 = [];

for (let i = 0; i < 100; i++) {
    let dt = [
        "",
        10000,
        false,
        () => {
            return true;
        }, {
            str: "",
            num: 10000,
            bool: false,
            foo: () => {
                return true;
            }
        }
    ];
    data2.push(dt[Math.floor(Math.random() * dt.length)])
}


let process_data_2 = deepClone(data2);

let result_2 = process_data_2 !== data2 && !process_data_2?.some((item) => {
    if (typeof item === "function") {
        return !!!item?.();
    } else if (typeof item === "object") {
        return !!!item?.foo?.()
    }
});


if (result_1 && result_2) {
    console.log("success")
} else {
    console.log("failed")
}






