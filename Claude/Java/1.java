// UserQueryServlet.java
import jakarta.servlet.http.*;
import java.io.*; import java.sql.*;

@WebServlet("/user")
public class UserQueryServlet extends HttpServlet {

    private static final String DB_URL = "jdbc:mysql://localhost/myapp";
    private static final String DB_USER = "appuser", DB_PASS = "secret";

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse res)
            throws IOException {

        String username = req.getParameter("username");
        res.setContentType("text/html;charset=UTF-8");
        PrintWriter out = res.getWriter();

        if (username == null || username.isBlank()) {
            res.sendError(400, "Missing 'username' parameter");
            return;
        }

        // ✔ PreparedStatement — parameter never interpolated into SQL
        String sql = "SELECT id, username, email FROM users WHERE username = ?";

        try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASS);
             PreparedStatement ps = conn.prepareStatement(sql)) {

            ps.setString(1, username);
            ResultSet rs = ps.executeQuery();

            if (rs.next()) {
                out.println("<p>User: " + escHtml(rs.getString("username"))
                           + " | Email: " + escHtml(rs.getString("email")) + "</p>");
            } else {
                out.println("<p>User not found.</p>");
            }

        } catch (SQLException e) {
            res.sendError(500, "Database error.");
        }
    }

    private String escHtml(String s) {
        return s.replace("&","&amp;").replace("<","&lt;")
                .replace(">","&gt;").replace("\"","&quot;");
    }
}
