const fs = require('fs');
const input = fs.readFileSync(process.stdin.fd, 'utf8').trimEnd().split('\n').map((x, i) => ({ val: parseInt(x), initial: i }));

function decrypt(input) {
    const m = (a, b) => ((a % b) + b) % b;
    for (n = 0; n < input.length; n++) {
        const pos = input.findIndex(c => c.initial === n);
        const newPos = m(pos + input[pos].val - 1, input.length - 1) + 1;
        const inc = (pos < newPos) ? 1 : -1;

        const elem = input[pos];
        input.splice(pos, 1);
        input.splice(newPos, 0, elem);
    }
    return input;
};

const output1 = decrypt([...input]);
const zPos1 = output1.findIndex(c => c.val === 0);
let result1 = 0;
for (i = 1; i <= 3; i++) {
    result1 += output1[(zPos1 + i * 1000) % input.length].val;
}
console.log(result1);

let result2 = 0;
let output2 = [...input].map((x, i) => ({ val: x.val * 811589153, initial: i }));
for (let i = 0; i < 10; i++) {
    output2 = decrypt(output2);
}
const zPos2 = output2.findIndex(c => c.val === 0);
for (i = 1; i <= 3; i++) {
    result2 += output2[(zPos2 + i * 1000) % input.length].val;
}
console.log(result2);
