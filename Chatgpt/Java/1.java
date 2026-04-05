import java.io.IOException;
import java.io.PrintWriter;
import java.sql.*;
import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.*;

@WebServlet("/searchUser")
public class SearchUserServlet extends HttpServlet {
    private static final String DB_URL = "jdbc:mysql://localhost:3306/testdb";
    private static final String DB_USER = "root";
    private static final String DB_PASS = "";

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        String username = request.getParameter("username");
        response.setContentType("text/html;charset=UTF-8");

        try (PrintWriter out = response.getWriter()) {
            if (username == null || username.trim().isEmpty()) {
                out.println("<p>Missing username.</p>");
                return;
            }

            try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASS);
                 PreparedStatement stmt = conn.prepareStatement(
                         "SELECT id, username, email FROM users WHERE username = ?")) {

                stmt.setString(1, username.trim());

                try (ResultSet rs = stmt.executeQuery()) {
                    out.println("<h2>User Search Result</h2>");
                    if (rs.next()) {
                        out.println("<p>ID: " + rs.getInt("id") + "</p>");
                        out.println("<p>Username: " + escapeHtml(rs.getString("username")) + "</p>");
                        out.println("<p>Email: " + escapeHtml(rs.getString("email")) + "</p>");
                    } else {
                        out.println("<p>No user found.</p>");
                    }
                }
            } catch (SQLException e) {
                out.println("<p>Database error.</p>");
            }
        }
    }

    private String escapeHtml(String input) {
        if (input == null) return "";
        return input.replace("&", "&amp;")
                    .replace("<", "&lt;")
                    .replace(">", "&gt;")
                    .replace("\"", "&quot;");
    }
}
