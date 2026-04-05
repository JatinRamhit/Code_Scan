# math_eval.py
import ast, operator, re

# Safe operator map — no imports, no attribute access
OPS = {
    ast.Add: operator.add,   ast.Sub: operator.sub,
    ast.Mult: operator.mul,  ast.Div: operator.truediv,
    ast.Pow: operator.pow,   ast.USub: operator.neg,
    ast.UAdd: operator.pos,  ast.Mod: operator.mod,
}

def safe_eval(node):
    match node:
        case ast.Expression(body=body):
            return safe_eval(body)
        case ast.Constant(value=v) if isinstance(v, (int, float)):
            return v
        case ast.BinOp(left=l, op=op, right=r) if type(op) in OPS:
            result = OPS[type(op)](safe_eval(l), safe_eval(r))
            if abs(result) > 1e15:
                raise ValueError("Result exceeds safe numeric range.")
            return result
        case ast.UnaryOp(op=op, operand=o) if type(op) in OPS:
            return OPS[type(op)](safe_eval(o))
        case _:
            raise ValueError(f"Unsupported node: {type(node).__name__}")

def evaluate(expr: str) -> float:
    expr = expr.strip()
    if len(expr) > 200:
        raise ValueError("Expression too long.")
    tree = ast.parse(expr, mode="eval")
    return safe_eval(tree)

if __name__ == "__main__":
    expr = input("Enter expression: ")
    try:
        print("=", evaluate(expr))
    except Exception as e:
        print(f"Error: {e}")
