import java.nio.file.*;

public class FileLoader {
    private static final Path BASE_DIR = Paths.get("/var/www/uploads").toAbsolutePath();

    public String loadUserFile(String userPath) throws IOException {
        Path resolvedPath = BASE_DIR.resolve(userPath).normalize();

        // Check if the resulting path is still within the BASE_DIR
        if (!resolvedPath.startsWith(BASE_DIR)) {
            throw new SecurityException("Access Denied: Path Traversal Attempt");
        }

        return new String(Files.readAllBytes(resolvedPath));
    }
}
