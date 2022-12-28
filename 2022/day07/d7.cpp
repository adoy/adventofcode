#include <iostream>
#include <map>
#include <sstream>
#include <string>
#include <vector>

using namespace std;

class Directory {
  string name;
  Directory *parent;
  map<string, Directory *> children;
  map<string, int> files;

public:
  Directory(string name, Directory *parent = nullptr)
      : name(name), parent(parent) {}

  ~Directory() {
    for (const auto &child : children) {
      delete child.second;
    }
  }

  Directory *parent_dir() { return parent; }
  void createDirectory(const string name) {
    children[name] = new Directory(name, this);
  }
  void addFile(const string name, const unsigned int size) {
    files[name] = size;
  }
  Directory *getDirectory(const string name) const { return children.at(name); }

  vector<Directory *> getDirectories() const;

  unsigned int getSize() const;
};

vector<Directory *> Directory::getDirectories() const {
  vector<Directory *> directories;
  for (const auto &child : children) {
    directories.push_back(child.second);
    for (const auto &directory : child.second->getDirectories()) {
      directories.push_back(directory);
    }
  }
  return directories;
}

unsigned int Directory::getSize() const {
  unsigned int size{0};
  for (const auto &child : children) {
    size += child.second->getSize();
  }
  for (const auto &file : files) {
    size += file.second;
  }

  return size;
}

unsigned int part1(const Directory &root) {
  unsigned int result{0};
  for (const auto &directory : root.getDirectories()) {
    size_t size{directory->getSize()};
    if (size < 100000) {
      result += size;
    }
  }
  return result;
}

unsigned int part2(const Directory &root) {
  unsigned int requiredSize{30000000 - (70000000 - root.getSize())};
  unsigned int smallest{70000000};

  for (const auto &directory : root.getDirectories()) {
    unsigned int size{directory->getSize()};
    if (size >= requiredSize && size < smallest) {
      smallest = size;
    }
  }

  return smallest;
}

int main() {

  Directory root{"root"};
  Directory *cwd = &root;

  string line;
  while (getline(cin, line)) {
    if (line[0] == '$') {
      if (line.substr(0, 5) == "$ cd ") {
        string dir = line.substr(5);
        if (dir == "/") {
          cwd = &root;
        } else if (dir == "..") {
          cwd = cwd->parent_dir();
        } else {
          cwd = cwd->getDirectory(dir);
        }
      }
    } else if (line.substr(0, 4) == "dir ") {
      cwd->createDirectory(line.substr(4));
    } else {
      stringstream ss{line};
      unsigned int size;
      string filename;
      ss >> size >> filename;
      cwd->addFile(filename, size);
    }
  }

  cout << "Result 1: " << part1(root) << endl;
  cout << "Result 2: " << part2(root) << endl;
}
