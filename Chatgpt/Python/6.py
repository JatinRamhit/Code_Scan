import ast
import operator as op

ALLOWED_OPERATORS = {
    ast.Add: op.add,
    ast.Sub: op.sub,
    ast.Mult: op.mul,
    ast.Div: op.truediv,
    ast.FloorDiv: op.floordiv,
    ast.Mod: op.mod,
    ast.Pow: op.pow,
    ast.USub: op.neg,
    ast.UAdd: op.pos,
}

def safe_eval(node):
    if isinstance(node, ast.Constant) and isinstance(node.value, (int, float)):
        return node.value
    elif isinstance(node, ast.BinOp) and type(node.op) in ALLOWED_OPERATORS:
        left = safe_eval(node.left)
        right = safe_eval(node.right)
        return ALLOWED_OPERATORS[type(node.op)](left, right)
    elif isinstance(node, ast.UnaryOp) and type(node.op) in ALLOWED_OPERATORS:
        operand = safe_eval(node.operand)
        return ALLOWED_OPERATORS[type(node.op)](operand)
    else:
        raise ValueError("Unsupported expression")

def evaluate_expression(expr: str):
    tree = ast.parse(expr, mode="eval")
    return safe_eval(tree.body)

if __name__ == "__main__":
    expr = input("Enter a math expression: ").strip()

    try:
        result = evaluate_expression(expr)
        print("Result:", result)
    except Exception as e:
        print("Error:", str(e))
