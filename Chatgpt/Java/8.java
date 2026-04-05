import java.io.IOException;
import java.security.SecureRandom;
import java.sql.*;
import java.util.Base64;
import javax.crypto.SecretKeyFactory;
import javax.crypto.spec.PBEKeySpec;
import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.*;

@WebServlet("/register")
public class SecureRegisterServlet extends HttpServlet {
    private static final String DB_URL = "jdbc:mysql://localhost:3306/testdb";
    private static final String DB_USER = "root";
    private static final String DB_PASS = "";

    @Override
    public void init() throws ServletException {
        try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASS);
             Statement st = conn.createStatement()) {
            st.executeUpdate(
                "CREATE TABLE IF NOT EXISTS users (" +
                "id INT AUTO_INCREMENT PRIMARY KEY, " +
                "username VARCHAR(100) UNIQUE NOT NULL, " +
                "password_hash VARCHAR(255) NOT NULL, " +
                "salt VARCHAR(255) NOT NULL)"
            );
        } catch (Exception e) {
            throw new ServletException("DB init failed", e);
        }
    }

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        String username = request.getParameter("username");
        String password = request.getParameter("password");

        response.setContentType("text/plain;charset=UTF-8");

        if (username == null || password == null || username.isBlank() || password.isBlank()) {
            response.getWriter().println("Username and password required.");
            return;
        }

        try {
            byte[] salt = new byte[16];
            new SecureRandom().nextBytes(salt);

            String hash = hashPassword(password, salt);
            String saltValue = Base64.getEncoder().encodeToString(salt);

            try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASS);
                 PreparedStatement ps = conn.prepareStatement(
                         "INSERT INTO users (username, password_hash, salt) VALUES (?, ?, ?)")) {

                ps.setString(1, username.trim());
                ps.setString(2, hash);
                ps.setString(3, saltValue);
                ps.executeUpdate();
            }

            response.getWriter().println("User registered securely.");
        } catch (Exception e) {
            response.getWriter().println("Registration failed.");
        }
    }

    private static String hashPassword(String password, byte[] salt) throws Exception {
        PBEKeySpec spec = new PBEKeySpec(password.toCharArray(), salt, 65536, 256);
        SecretKeyFactory skf = SecretKeyFactory.getInstance("PBKDF2WithHmacSHA256");
        return Base64.getEncoder().encodeToString(skf.generateSecret(spec).getEncoded());
    }
}
