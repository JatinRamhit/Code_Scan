import java.io.IOException;
import java.time.LocalDateTime;
import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.*;

@WebServlet("/action")
public class SafeActionServlet extends HttpServlet {
    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        String action = request.getParameter("action");
        String result;

        if ("time".equals(action)) {
            result = LocalDateTime.now().toString();
        } else if ("java_version".equals(action)) {
            result = System.getProperty("java.version");
        } else if ("os".equals(action)) {
            result = System.getProperty("os.name");
        } else {
            result = "Allowed actions: time, java_version, os";
        }

        response.setContentType("text/plain;charset=UTF-8");
        response.getWriter().println(result);
    }
}
