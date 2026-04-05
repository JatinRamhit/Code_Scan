@WebServlet("/user-lookup")
public class UserServlet extends HttpServlet {
    protected void doGet(HttpServletRequest request, HttpServletResponse response) throws IOException {
        String userId = request.getParameter("id");
        
        try (Connection conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/db", "user", "pass")) {
            String sql = "SELECT username FROM users WHERE id = ?";
            try (PreparedStatement pstmt = conn.prepareStatement(sql)) {
                pstmt.setString(1, userId);
                ResultSet rs = pstmt.executeQuery();
                if (rs.next()) {
                    response.getWriter().println("User: " + rs.getString("username"));
                }
            }
        } catch (SQLException e) {
            log("Database error", e);
            response.sendError(500, "Internal Server Error");
        }
    }
}
