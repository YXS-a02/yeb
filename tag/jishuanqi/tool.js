// 当前输入的值，初始值为0
let currentInput = "0";
 
// 当前操作符，默认为null
let currentOperator = null;
 
// 第一操作数，默认为null
let firstOperand = null;
 
// 将数字或小数点添加到显示屏上
function appendToDisplay(value) {
  if (currentInput === "0" && value !== ".") {
    // 如果当前输入为0，并且value不是小数点，直接用value替换currentInput
    currentInput = value;
  } else {
    // 否则，在currentInput后面追加value
    currentInput += value;
  }
  // 更新显示屏上的内容
  document.getElementById("display").innerText = currentInput;
}
 
// 清空显示屏
function clearDisplay() {
  currentInput = "0";
  document.getElementById("display").innerText = currentInput;
}
 
// 记录当前操作符，并将当前输入值作为第一操作数
function operate(operator) {
  if (currentOperator !== null) {
    // 如果当前已经记录有操作符，则先计算结果
    calculate();
  }
  // 记录第一操作数为当前输入值
  firstOperand = currentInput;
 
  // 记录当前操作符
  currentOperator = operator;
 
  // 将当前输入值重置为0
  currentInput = "0";
}
 
// 根据当前操作符和第二操作数计算结果，并将结果显示在屏幕上
function calculate() {
  // 如果当前未记录操作符或第一操作数，则直接返回
  if (currentOperator === null || firstOperand === null) return;
 
  let result;
 
  // 获取第二操作数
  const secondOperand = currentInput;
 
  // 根据当前操作符进行计算
  switch (currentOperator) {
    case "+":
      result = parseFloat(firstOperand) + parseFloat(secondOperand);
      break;
    case "-":
      result = parseFloat(firstOperand) - parseFloat(secondOperand);
      break;
    case "*":
      result = parseFloat(firstOperand) * parseFloat(secondOperand);
      break;
    case "/":
      result = parseFloat(firstOperand) / parseFloat(secondOperand);
      break;
    default:
      // 如果操作符不是加、减、乘、除，则直接返回
      return;
  }
 
  // 将计算结果更新为当前输入值
  currentInput = result.toString();
 
  // 重置当前操作符和第一操作数
  currentOperator = null;
  firstOperand = null;
 
  // 更新显示屏上的内容
  document.getElementById("display").innerText = currentInput;
}

function esc(){
  location.href="../../home.html"
}