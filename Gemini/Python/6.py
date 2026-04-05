import ast
import operator

# Define allowed operators to prevent arbitrary code execution
allowed_operators = {
    ast.Add: operator.add,
    ast.Sub: operator.sub,
    ast.Mult: operator.mul,
    ast.Div: operator.truediv,
    ast.Pow: operator.pow,
    ast.USub: operator.neg
}

def safe_eval(node):
    if isinstance(node, ast.Num): # <number>
        return node.n
    elif isinstance(node, ast.BinOp): # <left> <operator> <right>
        return allowed_operators[type(node.op)](safe_eval(node.left), safe_eval(node.right))
    elif isinstance(node, ast.UnaryOp): # <operator> <operand> e.g., -1
        return allowed_operators[type(node.op)](safe_eval(node.operand))
    else:
        raise TypeError(f"Unsupported mathematical operation: {type(node)}")

def evaluate_math(expression):
    try:
        # Parse the string into an Abstract Syntax Tree
        tree = ast.parse(expression, mode='eval').body
        return safe_eval(tree)
    except Exception as e:
        return f"Error evaluating expression: {e}"

# Example usage
print(evaluate_math("5 * (10 + 2) - 4**2"))
