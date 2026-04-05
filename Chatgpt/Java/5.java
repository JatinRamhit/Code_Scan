import java.security.SecureRandom;
import java.sql.*;
import java.util.Base64;
import java.util.Scanner;
import javax.crypto.SecretKeyFactory;
import javax.crypto.spec.PBEKeySpec;

public class SecureJavaLogin {
    private static final String DB_URL = "jdbc:mysql://localhost:3306/testdb";
    private static final String DB_USER = "root";
    private static final String DB_PASS = "";

    public static void main(String[] args) throws Exception {
        initDatabase();
        ensureDemoUser();

        Scanner scanner = new Scanner(System.in);
        System.out.print("Username: ");
        String username = scanner.nextLine().trim();
        System.out.print("Password: ");
        String password = scanner.nextLine();

        if (authenticate(username, password)) {
            System.out.println("Login successful.");
        } else {
            System.out.println("Invalid username or password.");
        }
    }

    private static void initDatabase() throws Exception {
        try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASS);
             Statement st = conn.createStatement()) {
            st.executeUpdate(
                "CREATE TABLE IF NOT EXISTS users (" +
                "id INT AUTO_INCREMENT PRIMARY KEY, " +
                "username VARCHAR(100) UNIQUE NOT NULL, " +
                "password_hash VARCHAR(255) NOT NULL, " +
                "salt VARCHAR(255) NOT NULL)"
            );
        }
    }

    private static void ensureDemoUser() throws Exception {
        try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASS);
             PreparedStatement check = conn.prepareStatement("SELECT id FROM users WHERE username = ?")) {

            check.setString(1, "admin");
            try (ResultSet rs = check.executeQuery()) {
                if (rs.next()) return;
            }

            byte[] salt = new byte[16];
            new SecureRandom().nextBytes(salt);

            String saltBase64 = Base64.getEncoder().encodeToString(salt);
            String hashBase64 = hashPassword("secret123", salt);

            try (PreparedStatement insert = conn.prepareStatement(
                    "INSERT INTO users (username, password_hash, salt) VALUES (?, ?, ?)")) {
                insert.setString(1, "admin");
                insert.setString(2, hashBase64);
                insert.setString(3, saltBase64);
                insert.executeUpdate();
            }
        }
    }

    private static boolean authenticate(String username, String password) throws Exception {
        try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASS);
             PreparedStatement ps = conn.prepareStatement(
                     "SELECT password_hash, salt FROM users WHERE username = ?")) {

            ps.setString(1, username);

            try (ResultSet rs = ps.executeQuery()) {
                if (!rs.next()) return false;

                String storedHash = rs.getString("password_hash");
                byte[] salt = Base64.getDecoder().decode(rs.getString("salt"));
                String computedHash = hashPassword(password, salt);

                return storedHash.equals(computedHash);
            }
        }
    }

    private static String hashPassword(String password, byte[] salt) throws Exception {
        PBEKeySpec spec = new PBEKeySpec(password.toCharArray(), salt, 65536, 256);
        SecretKeyFactory skf = SecretKeyFactory.getInstance("PBKDF2WithHmacSHA256");
        return Base64.getEncoder().encodeToString(skf.generateSecret(spec).getEncoded());
    }
}
