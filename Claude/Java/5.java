// SecureLoginServlet.java
import jakarta.servlet.http.*;
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import java.io.*; import java.sql.*;

@WebServlet("/login")
public class SecureLoginServlet extends HttpServlet {

    private static final BCryptPasswordEncoder ENC = new BCryptPasswordEncoder(12);
    private static final String DB_URL  = "jdbc:mysql://localhost/myapp";
    private static final String DB_USER = "appuser", DB_PASS = "secret";

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse res)
            throws IOException {

        String username = req.getParameter("username");
        String password = req.getParameter("password");

        if (username == null || password == null
                || username.isBlank() || password.isBlank()) {
            res.sendError(400, "Username and password are required.");
            return;
        }

        String sql = "SELECT id, username, password_hash FROM users WHERE username = ?";

        try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASS);
             PreparedStatement ps = conn.prepareStatement(sql)) {

            ps.setString(1, username);
            ResultSet rs = ps.executeQuery();

            if (rs.next() && ENC.matches(password, rs.getString("password_hash"))) {
                HttpSession session = req.getSession(true); // new session ID
                session.invalidate();                           // prevent fixation
                session = req.getSession(true);
                session.setAttribute("userId",   rs.getInt("id"));
                session.setAttribute("username", rs.getString("username"));
                res.sendRedirect("/dashboard");
            } else {
                res.sendError(401, "Invalid credentials.");
            }

        } catch (SQLException e) {
            res.sendError(500, "Server error.");
        }
    }
}
