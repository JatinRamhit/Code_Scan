// SafeFileLoader.java
import java.io.*; import java.nio.file.*;

public class SafeFileLoader {

    // Only files inside this directory may be read
    private static final Path ALLOWED =
        Path.of("/var/app/public_files").toRealPath();

    public static String readFile(String userInput) throws IOException {
        Path target = ALLOWED.resolve(userInput).normalize();

        // Confirm resolved path is still inside the allowed directory
        if (!target.startsWith(ALLOWED)) {
            throw new SecurityException("Access denied: path traversal detected.");
        }

        if (!Files.isRegularFile(target)) {
            throw new FileNotFoundException("File not found: " + userInput);
        }

        long size = Files.size(target);
        if (size > 1_048_576L) {  // 1 MB cap
            throw new IOException("File exceeds 1 MB read limit.");
        }

        return Files.readString(target);
    }

    public static void main(String[] args) throws Exception {
        BufferedReader br = new BufferedReader(new InputStreamReader(System.in));
        System.out.print("Filename: ");
        String path = br.readLine().strip();
        try {
            System.out.println(readFile(path));
        } catch (SecurityException | FileNotFoundException | IOException e) {
            System.err.println("Error: " + e.getMessage());
        }
    }
}
