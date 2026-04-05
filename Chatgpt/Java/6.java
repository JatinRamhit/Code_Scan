import java.io.IOException;
import java.util.Map;
import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.*;

@WebServlet("/go")
public class SafeRedirectServlet extends HttpServlet {
    private static final Map<String, String> ALLOWED_ROUTES = Map.of(
        "home", "/home.jsp",
        "profile", "/profile.jsp",
        "help", "/help.jsp"
    );

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        String page = request.getParameter("page");
        String destination = ALLOWED_ROUTES.getOrDefault(page, "/home.jsp");
        response.sendRedirect(request.getContextPath() + destination);
    }
}
